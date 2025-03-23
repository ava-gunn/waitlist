<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProjectRepository
{
    public function create(array $data): Project|bool
    {
        // Validate subdomain and return false if validation fails
        if (! $this->validateSubdomain($data)) {
            return false;
        }

        $project = new Project($data);

        if (isset($data['logo'])) {
            $project->logo_path = $this->storeLogo($data['logo']);
        }

        $project->save();

        return $project;
    }

    public function update(Project $project, array $data): Project
    {
        // Validate subdomain with exclusion for current project
        $this->validateSubdomain($data, $project->id);

        if (isset($data['logo'])) {
            // Delete old logo if it exists
            if ($project->logo_path) {
                Storage::delete($project->logo_path);
            }

            $project->logo_path = $this->storeLogo($data['logo']);
            unset($data['logo']);
        }

        $project->fill($data);
        $project->save();

        return $project;
    }

    public function delete(Project $project): bool
    {
        // Delete the logo if it exists
        if ($project->logo_path) {
            Storage::delete($project->logo_path);
        }

        return (bool) $project->delete();
    }

    public function findByUser($userId): Collection
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        }

        return Project::where('user_id', $userId)->get();
    }

    private function validateSubdomain(array $data, ?int $excludeId = null): bool
    {
        if (! isset($data['subdomain'])) {
            return true; // No subdomain to validate
        }

        $rules = [
            'subdomain' => 'required|alpha_dash|min:3|max:30|unique:projects,subdomain',
        ];

        if ($excludeId) {
            $rules['subdomain'] .= ',' . $excludeId;
        }

        $validator = Validator::make(['subdomain' => $data['subdomain']], $rules);

        if ($validator->fails()) {
            // For updates, we still want to throw an exception
            if ($excludeId) {
                throw new ValidationException($validator);
            }

            // For creates, we just return false
            return false;
        }

        return true;
    }

    private function storeLogo($logo): string
    {
        return $logo->store('project-logos', 'public');
    }
}
