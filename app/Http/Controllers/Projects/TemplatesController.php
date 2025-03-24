<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WaitlistTemplate;
use App\Repositories\WaitlistTemplateRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplatesController extends Controller
{
    protected WaitlistTemplateRepository $templateRepository;

    public function __construct(WaitlistTemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Display a listing of templates for a project
     */
    public function index(Project $project): \Inertia\Response
    {
        $this->authorize('view', $project);

        $templates = WaitlistTemplate::where('is_active', true)->get();

        return Inertia::render('Projects/Templates/Index', [
            'project' => $project,
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for editing template customizations
     */
    public function edit(Project $project, WaitlistTemplate $template): \Inertia\Response
    {
        $this->authorize('update', $project);

        // Make sure we have the waitlistTemplate relationship loaded
        $project->load('waitlistTemplate');

        // Initialize template_customizations if null
        if ($project->template_customizations === null) {
            $project->template_customizations = [];
        }

        return Inertia::render('Projects/Templates/Edit', [
            'project' => $project,
            'template' => $template,
        ]);
    }

    /**
     * Update template customizations
     */
    public function update(Request $request, Project $project, WaitlistTemplate $template): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'customizations' => 'required|array',
        ]);

        $this->templateRepository->updateForProject(
            $project,
            $template,
            [
                'customizations' => $validated['customizations'],
            ]
        );

        return redirect()->back()->with('success', 'Template customizations updated successfully');
    }

    /**
     * Set a template for a project
     */
    public function setTemplate(Project $project, WaitlistTemplate $template): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);
        $this->templateRepository->setTemplateForProject($project, $template);

        return redirect()->route('projects.show', $project->id)->with('success', 'Template set successfully');
    }

    /**
     * Remove template from a project
     */
    public function removeTemplate(Project $project): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);
        $this->templateRepository->removeFromProject($project);

        return redirect()->back()->with('success', 'Template removed successfully');
    }
}
