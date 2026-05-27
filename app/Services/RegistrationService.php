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
use App\Models\RegistrationCode;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    public function __construct(
        private readonly RegistrationCodeRepositoryInterface $codeRepository,
    ) {}

    /**
     * Validate an incoming registration code and return prefill data.
     *
     * @return array
     * @throws ValidationException
     */
    public function validateCode(string $code): array
    {
        $registrationCode = $this->codeRepository->findByCode($code);

        if (! $registrationCode) {
            Log::warning('registration.invalid_code', ['code' => $code]);
            throw ValidationException::withMessages([
                'code' => 'This code is not recognised. Please check and try again, or contact the secretariat.',
            ]);
        }

        if ($registrationCode->status !== CodeStatus::ACTIVE) {
            Log::warning('registration.code_not_active', [
                'code'   => $code,
                'status' => $registrationCode->status->value,
            ]);
            throw ValidationException::withMessages([
                'code' => $registrationCode->status->userMessage(),
            ]);
        }

        return [
            'code'              => $registrationCode->code,
            'prefill_name'      => $registrationCode->prefill_name,
            'prefill_phone'     => $registrationCode->prefill_phone,
            'prefill_category'  => $registrationCode->prefill_category,
            'prefill_church_id' => $registrationCode->prefill_church_id,
            'amount_paid'       => $registrationCode->amount_paid,
            'payment_type'      => $registrationCode->payment_type->label(),
        ];
    }

    /**
     * Complete a camper's registration.
     *
     * @param  array  $data
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

            // ── 2. Determine category ─────────────────────────────────────────
            $lockedCategory = $registrationCode->prefill_category
                ?? ($data['locked_category'] ?? null)
                ?? ($data['category_locked'] ?? null);

            if (! $lockedCategory) {
                throw ValidationException::withMessages([
                    'code' => 'Registration category could not be determined. Please contact the secretariat.',
                ]);
            }

            $category = CamperCategory::from($lockedCategory);

            // ── 3. Create the Camper record ───────────────────────────────────
            $camper = Camper::create([
                'registration_code_id' => $registrationCode->id,
                'camper_number'        => $registrationCode->code,
                'full_name'            => $registrationCode->prefill_name,
                'phone'                => $registrationCode->prefill_phone,
                'date_of_birth'        => isset($data['date_of_birth']) && $data['date_of_birth'] !== ''
                    ? $data['date_of_birth']
                    : null,
                'gender'               => $data['gender'],
                'category'             => $category,
                'home_address'         => $data['home_address'] ?? null,
                'church_id'            => $registrationCode->prefill_church_id ?? $data['church_id'],
                'ministry'             => $data['ministry'] ?? null,
                'club_rank'            => $data['club_rank'] ?? null,
            ]);

            // ── 4. Attach photo ───────────────────────────────────────────────
            // photo_contents = JPEG bytes pre-converted by RegistrationController::toJpeg()
            // This guarantees DomPDF always receives JPEG regardless of original upload format.
            if (! empty($data['photo_contents'])) {
                try {
                    $camper->addMediaFromString($data['photo_contents'])
                        ->usingFileName('photo.jpg')
                        ->toMediaCollection('photo', 'public');  // explicit disk

                    Log::info('registration.photo_attached', ['camper_id' => $camper->id]);
                } catch (\Throwable $e) {
                    Log::warning('registration.photo_failed', [
                        'camper_id' => $camper->id,
                        'error'     => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('registration.no_photo', ['camper_id' => $camper->id]);
            }

            // ── 5. Create health record ───────────────────────────────────────
            CamperHealth::create([
                'camper_id'          => $camper->id,
                'medical_conditions' => $data['medical_conditions'] ?? null,
                'medications'        => $data['medications'] ?? null,
                'allergies'          => $data['allergies'] ?? null,
            ]);

            // ── 6. Create parent/guardian contact ─────────────────────────────
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

            // ── 7. Mark code as CLAIMED ───────────────────────────────────────
            $this->codeRepository->markAsClaimed($registrationCode);

            Log::info('registration.complete', [
                'camper_number' => $camper->camper_number,
                'category'      => $camper->category->value,
                'church_id'     => $camper->church_id,
                'payment_type'  => $registrationCode->payment_type->value,
            ]);

            // ── 8. Dispatch async jobs ────────────────────────────────────────
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
