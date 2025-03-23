<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectRepository $projectRepository
    ) {}

    public function index(): Response
    {
        $projects = auth()->user()->projects()->latest()->get();

        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($projects),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = $this->projectRepository->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return Redirect::route('projects.show', $project)
            ->with('success', 'Project created successfully');
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['waitlistTemplates', 'signups' => fn ($query) => $query->latest()->limit(10)]);

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

        return round(($verifiedSignups / $totalSignups) * 100, 2);
    }
}
