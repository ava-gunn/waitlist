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

        $template->load('projects');

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
            'is_active' => 'boolean',
        ]);

        $this->templateRepository->updateForProject(
            $project,
            $template,
            [
                'customizations' => $validated['customizations'],
                'is_active' => $validated['is_active'] ?? false,
            ]
        );

        if (isset($validated['is_active']) && $validated['is_active']) {
            $this->templateRepository->activateForProject($project, $template);
        }

        return redirect()->back()->with('success', 'Template customizations updated successfully');
    }

    /**
     * Activate a template for a project
     */
    public function activate(Project $project, WaitlistTemplate $template): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);
        $this->templateRepository->activateForProject($project, $template);

        return redirect()->back()->with('success', 'Template activated successfully');
    }

    /**
     * Deactivate a template for a project
     */
    public function deactivate(Project $project, WaitlistTemplate $template): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);
        $this->templateRepository->deactivateForProject($project, $template);

        return redirect()->back()->with('success', 'Template deactivated successfully');
    }
}
