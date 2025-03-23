<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\WaitlistTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class WaitlistTemplateRepository
{
    /**
     * Activate a template for a project
     */
    public function activateForProject(Project $project, WaitlistTemplate $template): bool
    {
        // First deactivate all templates for this project
        $this->deactivateAllForProject($project);

        // Then activate the requested template
        return (bool) $project->waitlistTemplates()->syncWithoutDetaching([
            $template->id => ['is_active' => true, 'customizations' => json_encode([])],
        ]);
    }

    /**
     * Update template customizations for a project
     */
    public function updateForProject(Project $project, WaitlistTemplate $template, array $data): bool
    {
        $pivotData = [];

        // Ensure we're properly handling the customizations data
        if (isset($data['customizations']) && is_array($data['customizations'])) {
            $pivotData['customizations'] = json_encode($data['customizations']);
        }

        if (isset($data['is_active'])) {
            $pivotData['is_active'] = (bool) $data['is_active'];
        }

        // Debug log to check what's being saved
        Log::debug('Updating template customizations', [
            'project_id' => $project->id,
            'template_id' => $template->id,
            'pivotData' => $pivotData,
        ]);

        // Check if the relationship exists first
        $exists = $project->waitlistTemplates()->where('waitlist_template_id', $template->id)->exists();

        if (! $exists) {
            // Create the relationship if it doesn't exist
            return (bool) $project->waitlistTemplates()->attach($template->id, $pivotData);
        }

        // Update the existing relationship
        return (bool) $project->waitlistTemplates()->updateExistingPivot(
            $template->id,
            $pivotData
        );
    }

    /**
     * Deactivate a template for a project
     */
    public function deactivateForProject(Project $project, WaitlistTemplate $template): bool
    {
        return (bool) $project->waitlistTemplates()->updateExistingPivot(
            $template->id,
            ['is_active' => false]
        );
    }

    /**
     * Deactivate all templates for a project
     */
    public function deactivateAllForProject(Project $project): bool
    {
        $templates = $project->waitlistTemplates;

        foreach ($templates as $template) {
            $project->waitlistTemplates()->updateExistingPivot(
                $template->id,
                ['is_active' => false]
            );
        }

        return true;
    }

    /**
     * Find all active templates
     */
    public function findAllActive(): Collection
    {
        return WaitlistTemplate::where('is_active', true)->get();
    }

    /**
     * Find the active template for a project
     */
    public function findActiveForProject(Project $project): ?WaitlistTemplate
    {
        $projectTemplate = $project->waitlistTemplates()
            ->wherePivot('is_active', true)
            ->first();

        return $projectTemplate;
    }
}
