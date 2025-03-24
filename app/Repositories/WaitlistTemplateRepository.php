<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\WaitlistTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class WaitlistTemplateRepository
{
    /**
     * Set a template for a project
     */
    public function setTemplateForProject(Project $project, WaitlistTemplate $template): bool
    {
        $project->waitlist_template_id = $template->id;
        $project->template_customizations = [];

        return $project->save();
    }

    /**
     * Update template customizations for a project
     */
    public function updateForProject(Project $project, WaitlistTemplate $template, array $data): bool
    {
        // Ensure we're properly handling the customizations data
        if (isset($data['customizations']) && is_array($data['customizations'])) {
            $project->template_customizations = $data['customizations'];
        }

        // Debug log to check what's being saved
        Log::debug('Updating template customizations', [
            'project_id' => $project->id,
            'template_id' => $template->id,
            'customizations' => $project->template_customizations,
        ]);

        return $project->save();
    }

    /**
     * Remove a template from a project
     */
    public function removeFromProject(Project $project): bool
    {
        $project->waitlist_template_id = null;
        $project->template_customizations = null;

        return $project->save();
    }

    /**
     * Get all templates
     */
    public function all(): Collection
    {
        return WaitlistTemplate::where('is_active', true)->get();
    }

    /**
     * Get all active templates
     */
    public function getActive(): Collection
    {
        return WaitlistTemplate::active()->get();
    }

    /**
     * Get templates for a project
     */
    public function getForProject(Project $project): ?WaitlistTemplate
    {
        return $project->waitlistTemplate;
    }
}
