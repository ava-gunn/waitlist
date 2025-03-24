<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('waitlist_template_id')->nullable()->constrained('waitlist_templates')->nullOnDelete();
            $table->json('template_customizations')->nullable();
        });

        // Migrate active templates for each project
        $this->migrateActiveTemplatesForProjects();
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('waitlist_template_id');
            $table->dropColumn('template_customizations');
        });
    }

    /**
     * Migrate the currently active templates for each project to the new direct relationship
     */
    private function migrateActiveTemplatesForProjects(): void
    {
        // Get all projects
        $projects = Project::all();

        foreach ($projects as $project) {
            // Find the active template for this project from the pivot table
            $activeTemplate = DB::table('project_waitlist_template')
                ->where('project_id', $project->id)
                ->where('is_active', true)
                ->first();

            if ($activeTemplate) {
                // Update the project with the active template ID and customizations
                $project->waitlist_template_id = $activeTemplate->waitlist_template_id;
                $project->template_customizations = $activeTemplate->customizations;
                $project->save();
            }
        }
    }
};
