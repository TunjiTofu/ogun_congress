<?php

namespace App\Http\Requests;

use App\Enums\ContactType;
use App\Enums\Gender;
use App\Models\CamperContact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SubmitRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Code (used to look up pre-fill data — submitted values for name/phone are ignored)
            'code'                  => ['required', 'string', 'exists:registration_codes,code'],

            // Step 1 — Personal
            'date_of_birth'         => ['nullable', 'date', 'before:today', 'after:' . now()->subYears(100)->toDateString()],
            'gender'                => ['required', new Enum(Gender::class)],
            'home_address'          => ['nullable', 'string', 'max:500'],
            'photo'                 => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],

            // Step 2 — Church & Ministry
            'church_id'             => ['nullable', 'integer', 'exists:churches,id'],
            'ministry'              => ['nullable', 'string', 'max:100'],
            'club_rank'             => ['nullable', 'string', 'max:100'],
            'volunteer_role'        => ['nullable', 'string', 'max:100'],

            // Step 3 — Parent/Guardian (conditionally required for under-16)
            // Full conditional enforcement happens in the Service layer via CamperCategory.
            // We validate presence here if the client sends these fields.
            'parent_name'           => ['nullable', 'string', 'max:191'],
            'parent_relationship'   => ['nullable', 'string', 'max:50'],
            'parent_phone'          => ['nullable', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'parent_email'          => ['nullable', 'email', 'max:191'],

            // Step 3 — Health (all optional — user may skip entirely)
            'medical_conditions'    => ['nullable', 'string'],
            'medications'           => ['nullable', 'string'],
            'allergies'             => ['nullable', 'string'],

            // Step 4 — Parent/Guardian (conditionally required for under-16)
            'parent_name'           => ['nullable', 'string', 'max:191'],
            'parent_relationship'   => ['nullable', 'string', 'max:50'],
            'parent_phone'          => ['nullable', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'parent_email'          => ['nullable', 'email', 'max:191'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before'        => 'Date of birth must be in the past.',
            'church_id.exists'            => 'Please select a valid church.',
            'photo.mimes'                 => 'Photo must be a JPEG or PNG image.',
            'photo.max'                   => 'Photo must be smaller than 2MB.',
            'parent_phone.regex'          => 'Please enter a valid Nigerian phone number.',
            'emergency_phone.regex'       => 'Please enter a valid Nigerian phone number.',
            'doctor_phone.regex'          => 'Please enter a valid Nigerian phone number.',
        ];
    }
}
