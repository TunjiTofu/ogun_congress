<?php

namespace App\Services;

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Enums\ContactType;
use App\Jobs\GenerateCamperDocumentsJob;
use App\Jobs\SendRegistrationConfirmationSmsJob;
use App\Models\Camper;
use App\Models\CamperContact;
use App\Models\CamperHealth;
use App\Models\Church;
use App\Models\RegistrationCode;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    public function __construct(
        private readonly RegistrationCodeRepositoryInterface $codeRepository,
    ) {}

    /**
     * Validate an incoming registration code and return prefill data.
     *
     * @return array{ code: string, prefill_name: string, prefill_phone: string, amount_paid: float|null, payment_type: string }
     * @throws ValidationException
     */
    public function validateCode(string $code): array
    {
        $registrationCode = $this->codeRepository->findByCode($code);

        if (! $registrationCode) {
            throw ValidationException::withMessages([
                'code' => 'This code is not recognised. Please check and try again, or contact the secretariat.',
            ]);
        }

        if ($registrationCode->status !== CodeStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'code' => $registrationCode->status->userMessage(),
            ]);
        }

        return [
            'code'         => $registrationCode->code,
            'prefill_name' => $registrationCode->prefill_name,
            'prefill_phone'=> $registrationCode->prefill_phone,
            'amount_paid'  => $registrationCode->amount_paid,
            'payment_type' => $registrationCode->payment_type->label(),
        ];
    }

    /**
     * Complete a camper's registration.
     *
     * All writes happen inside a single DB transaction.
     * The code is locked for update to prevent concurrent submissions.
     *
     * @param  array  $data  Validated data from SubmitRegistrationRequest
     * @return Camper
     * @throws ValidationException|\Throwable
     */
    public function submit(array $data): Camper
    {
        return DB::transaction(function () use ($data) {

            // ── 1. Lock and re-validate the code ──────────────────────────────
            $registrationCode = $this->codeRepository->lockForUpdate($data['code']);

            if (! $registrationCode) {
                throw ValidationException::withMessages([
                    'code' => 'This code is not recognised. Please contact the secretariat.',
                ]);
            }

            if ($registrationCode->status !== CodeStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'code' => $registrationCode->status->userMessage(),
                ]);
            }

            // ── 2. Compute category from DOB (server-side, never from client) ─
            $dob      = Carbon::parse($data['date_of_birth']);
            $age      = $dob->age;
            $category = CamperCategory::fromAge($age);

            // ── 3. Create the Camper record ───────────────────────────────────
            // Full name and phone are always pulled from the registration code —
            // any values submitted in the form POST are intentionally ignored.
            $camper = Camper::create([
                'registration_code_id' => $registrationCode->id,
                'camper_number'        => $registrationCode->code,
                'full_name'            => $registrationCode->prefill_name,
                'phone'                => $registrationCode->prefill_phone,
                'date_of_birth'        => $data['date_of_birth'],
                'gender'               => $data['gender'],
                'category'             => $category,
                'home_address'         => $data['home_address'] ?? null,
                'church_id'            => $data['church_id'],
                'ministry'             => $data['ministry'] ?? null,
                'club_rank'            => $data['club_rank'] ?? null,
                'volunteer_role'       => $data['volunteer_role'] ?? null,
            ]);

            // ── 4. Attach photo via Spatie MediaLibrary ───────────────────────
            if (! empty($data['photo'])) {
                $camper->addMedia($data['photo'])
                       ->toMediaCollection('photo');
            }

            // ── 5. Create health record (always, even if all fields are empty) ─
            CamperHealth::create([
                'camper_id'           => $camper->id,
                'medical_conditions'  => $data['medical_conditions'] ?? null,
                'medications'         => $data['medications'] ?? null,
                'allergies'           => $data['allergies'] ?? null,
                'dietary_restrictions'=> $data['dietary_restrictions'] ?? null,
                'doctor_name'         => $data['doctor_name'] ?? null,
                'doctor_phone'        => $data['doctor_phone'] ?? null,
                'insurance_details'   => $data['insurance_details'] ?? null,
            ]);

            // ── 6. Create parent/guardian contact (Adventurers & Pathfinders) ─
            if ($category->requiresParentalConsent()) {
                CamperContact::create([
                    'camper_id'    => $camper->id,
                    'type'         => ContactType::PARENT_GUARDIAN,
                    'full_name'    => $data['parent_name'],
                    'relationship' => $data['parent_relationship'],
                    'phone'        => $data['parent_phone'],
                    'email'        => $data['parent_email'] ?? null,
                    'is_primary'   => true,
                ]);
            }

            // ── 7. Create emergency contact ───────────────────────────────────
            CamperContact::create([
                'camper_id'    => $camper->id,
                'type'         => ContactType::EMERGENCY_CONTACT,
                'full_name'    => $data['emergency_name'],
                'relationship' => $data['emergency_relationship'],
                'phone'        => $data['emergency_phone'],
                'email'        => $data['emergency_email'] ?? null,
                'is_primary'   => true,
            ]);

            // ── 8. Mark code as CLAIMED ───────────────────────────────────────
            $this->codeRepository->markAsClaimed($registrationCode);

            // Transaction commits here ─────────────────────────────────────────

            // ── 9. Dispatch async jobs (outside transaction is fine — they are ─
            //        read-only in terms of registration data) ──────────────────
            GenerateCamperDocumentsJob::dispatch($camper->id);

            SendRegistrationConfirmationSmsJob::dispatch(
                phone:        $camper->phone,
                name:         $camper->full_name,
                camperNumber: $camper->camper_number,
            );

            return $camper;
        });
    }
}
