<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaitlistTemplateRequest;
use App\Http\Resources\WaitlistTemplateResource;
use App\Models\Project;
use App\Models\WaitlistTemplate;
use App\Repositories\WaitlistTemplateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WaitlistTemplateController extends Controller
{
    public function __construct(
        protected WaitlistTemplateRepository $templateRepository
    ) {}

    public function index(Project $project): Response
    {
        $this->authorize('update', $project);

        $templates = WaitlistTemplate::where('is_active', true)->get();

        return Inertia::render('Projects/Templates/Index', [
            'project' => $project,
            'templates' => WaitlistTemplateResource::collection($templates),
        ]);
    }

    public function edit(Project $project, WaitlistTemplate $template): Response
    {
        $this->authorize('update', $project);

        // Load the pivot data if the project has this template attached
        $project->load(['waitlistTemplates' => function ($query) use ($template) {
            $query->where('waitlist_template_id', $template->id);
        }]);

        return Inertia::render('Projects/Templates/Edit', [
            'project' => $project,
            'template' => new WaitlistTemplateResource($template),
        ]);
    }

    public function update(WaitlistTemplateRequest $request, Project $project, WaitlistTemplate $template): RedirectResponse
    {
        $this->authorize('update', $project);

        $this->templateRepository->updateForProject(
            $project,
            $template,
            $request->validated()
        );

        return Redirect::route('projects.show', $project)
            ->with('success', 'Template customized successfully');
    }

    public function activate(Project $project, WaitlistTemplate $template): RedirectResponse
    {
        $this->authorize('update', $project);

        // First deactivate all other templates for this project
        $this->templateRepository->deactivateAllForProject($project);

        // Then attach this template or update if already attached
        $this->templateRepository->activateForProject($project, $template);

        return Redirect::route('projects.templates.edit', [
            'project' => $project,
            'template' => $template,
        ])->with('success', 'Template activated successfully');
    }

    public function deactivate(Project $project, WaitlistTemplate $template): RedirectResponse
    {
        $this->authorize('update', $project);

        $this->templateRepository->deactivateForProject($project, $template);

        return Redirect::route('projects.show', $project)
            ->with('success', 'Template deactivated successfully');
    }
}
