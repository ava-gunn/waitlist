<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project') ? $this->route('project')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'min:3',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/',
                Rule::unique('projects', 'subdomain')->ignore($projectId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'settings' => ['nullable', 'array'],
            'settings.theme' => ['nullable', 'string', 'in:light,dark,auto'],
            'settings.collect_name' => ['nullable', 'boolean'],
            'settings.social_sharing' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'template_id' => ['nullable', 'integer', 'exists:waitlist_templates,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens. It cannot start or end with a hyphen.',
            'template_id.exists' => 'The selected template does not exist or is not available.',
        ];
    }
}
