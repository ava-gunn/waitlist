<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaitlistTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customizations' => ['required', 'array'],
            'customizations.heading' => ['nullable', 'string', 'max:100'],
            'customizations.description' => ['nullable', 'string', 'max:500'],
            'customizations.buttonText' => ['nullable', 'string', 'max:30'],
            'customizations.backgroundColor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'customizations.textColor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'customizations.buttonColor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'customizations.buttonTextColor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
