<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check for duplicate email for the same project
                    $project = Project::where('subdomain', $this->subdomain)
                        ->where('is_active', true)
                        ->first();

                    if (! $project) {
                        $fail('Project not found or inactive.');

                        return;
                    }

                    $exists = $project->signups()->where('email', $value)->exists();

                    if ($exists) {
                        $fail('This email is already on the waitlist.');
                    }
                },
            ],
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'subdomain' => $this->route('subdomain'),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
