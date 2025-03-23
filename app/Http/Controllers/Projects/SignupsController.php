<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Resources\SignupResource;
use App\Models\Project;
use App\Models\Signup;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SignupsController extends Controller
{
    /**
     * Display a listing of signups for a project
     */
    public function index(Project $project): \Inertia\Response
    {
        $this->authorize('view', $project);

        $signups = $project->signups()
            ->latest()
            ->paginate(10);

        return Inertia::render('Projects/Signups/Index', [
            'project' => $project,
            'signups' => SignupResource::collection($signups),
        ]);
    }

    /**
     * Export signups as CSV
     */
    public function export(Project $project): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('view', $project);

        $fileName = Str::slug($project->name) . '-waitlist-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ];

        $signups = $project->signups;

        $callback = function () use ($signups) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Name', 'Email', 'Signup Date', 'Verified']);

            // Add data
            foreach ($signups as $signup) {
                fputcsv($file, [
                    $signup->name,
                    $signup->email,
                    $signup->created_at->format('Y-m-d H:i:s'),
                    $signup->verified_at ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete a signup
     */
    public function destroy(Project $project, Signup $signup): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $project);

        // Ensure the signup belongs to this project
        if ($signup->project_id !== $project->id) {
            abort(403, 'This signup does not belong to the specified project.');
        }

        $signup->delete();

        return redirect()->back()->with('success', 'Signup deleted successfully');
    }
}
