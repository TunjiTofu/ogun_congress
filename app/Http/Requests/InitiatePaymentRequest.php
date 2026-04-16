<?php

namespace App\Http\Requests;

use App\Enums\CamperCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:191'],
            'phone'    => ['required', 'string', 'regex:/^(0|\+?234)[789][01]\d{8}$/'],
            'category' => ['required', new Enum(CamperCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Nigerian phone number (e.g. 08012345678).',
        ];
    }
}
