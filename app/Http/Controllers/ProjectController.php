<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\WaitlistTemplateResource;
use App\Models\Project;
use App\Models\WaitlistTemplate;
use App\Repositories\ProjectRepository;
use App\Repositories\WaitlistTemplateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectRepository $projectRepository,
        protected WaitlistTemplateRepository $templateRepository
    ) {}

    public function index(): Response
    {
        // Explicitly get the authenticated user to ensure we're working with the correct user
        $user = auth()->user();
        $projects = $user->projects()->latest()->with('signups')->withCount('signups')->get();

        // Log for debugging
        Log::info('User projects request', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'projects_count' => $projects->count(),
            'first_project' => $projects->first() ? $projects->first()->toArray() : null,
        ]);

        // Pass as a direct array instead of using the resource collection to simplify debugging
        return Inertia::render('Projects/Index', [
            'projects' => $projects->toArray(),
        ]);
    }

    public function create(): Response
    {
        $templates = WaitlistTemplate::where('is_active', true)->get();

        return Inertia::render('Projects/Create', [
            'templates' => WaitlistTemplateResource::collection($templates),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = $this->projectRepository->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        // Set the selected template for this project if provided
        if ($request->has('template_id')) {
            $template = WaitlistTemplate::findOrFail($request->template_id);
            $this->templateRepository->setTemplateForProject($project, $template);
        }

        return Redirect::route('projects.show', $project)
            ->with('success', 'Project created successfully');
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        // Load relationships
        $project->load(['waitlistTemplate', 'signups' => fn ($query) => $query->latest()->limit(10)]);

        // Initialize template_customizations if null
        if ($project->template_customizations === null) {
            $project->template_customizations = [];
        }

        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
            'stats' => [
                'total_signups' => $project->signups()->count(),
                'verified_signups' => $project->signups()->verified()->count(),
                'conversion_rate' => $this->calculateConversionRate($project),
                'daily_signups' => $project->signups()
                    ->whereDate('created_at', '>', now()->subDays(30))
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->get()
                    ->pluck('count', 'date'),
            ],
        ]);
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $this->projectRepository->update($project, $request->validated());

        return Redirect::route('projects.show', $project)
            ->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $this->projectRepository->delete($project);

        return Redirect::route('projects.index')
            ->with('success', 'Project deleted successfully');
    }

    private function calculateConversionRate(Project $project): float
    {
        $totalSignups = $project->signups()->count();
        if ($totalSignups === 0) {
            return 0;
        }

        $verifiedSignups = $project->signups()->verified()->count();

        return round(($verifiedSignups / $totalSignups) * 100, 1);
    }
}
