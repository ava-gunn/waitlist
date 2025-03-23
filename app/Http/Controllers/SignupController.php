<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Http\Resources\SignupResource;
use App\Models\Project;
use App\Models\Signup;
use App\Repositories\SignupRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SignupController extends Controller
{
    public function __construct(
        protected SignupRepository $signupRepository
    ) {}

    public function index(Project $project): Response
    {
        $this->authorize('viewSignups', $project);

        $signups = $project->signups()->latest()->paginate(50);

        return Inertia::render('Projects/Signups/Index', [
            'project' => $project,
            'signups' => SignupResource::collection($signups),
            'pagination' => [
                'total' => $signups->total(),
                'per_page' => $signups->perPage(),
                'current_page' => $signups->currentPage(),
                'last_page' => $signups->lastPage(),
            ],
        ]);
    }

    public function store(SignupRequest $request, string $subdomain): JsonResponse
    {
        // First check if project exists and is active
        $project = Project::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found or inactive.',
            ], 404);
        }

        try {
            $signup = $this->signupRepository->create([
                'project_id' => $project->id,
                'email' => $request->email,
                'name' => $request->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'verification_token' => Str::random(64),
            ]);

            // In a real app, we would send a verification email here
            // $this->signupRepository->sendVerificationEmail($signup);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for signing up!',
            ]);
        } catch (Exception $e) {
            Log::error('Signup error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'There was an error processing your signup.',
            ], 500);
        }
    }

    public function destroy(Project $project, Signup $signup): RedirectResponse
    {
        $this->authorize('deleteSignup', [$project, $signup]);

        $this->signupRepository->delete($signup);

        return back()->with('success', 'Signup removed successfully');
    }

    public function verify(string $token): RedirectResponse
    {
        $signup = Signup::where('verification_token', $token)->first();

        if (! $signup) {
            return redirect('/')->with('error', 'Invalid verification token.');
        }

        $project = $signup->project;

        if (! $project || ! $project->is_active) {
            return redirect('/')->with('error', 'This project is no longer active.');
        }

        $signup->markAsVerified();

        return redirect($project->full_url)->with('success', 'Your email has been verified. Thank you!');
    }

    public function export(Project $project): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('viewSignups', $project);

        $filename = Str::slug($project->name) . '-waitlist-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $signups = $project->signups;

        $callback = function () use ($signups) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['ID', 'Email', 'Name', 'Status', 'Signed Up', 'Verified At']);

            // Add rows
            foreach ($signups as $signup) {
                fputcsv($file, [
                    $signup->id,
                    $signup->email,
                    $signup->name,
                    $signup->verified_at ? 'Verified' : 'Pending',
                    $signup->created_at->format('Y-m-d H:i:s'),
                    $signup->verified_at ? $signup->verified_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
