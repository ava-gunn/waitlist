<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WaitlistLandingController extends Controller
{
    /**
     * Display the waitlist landing page for a project.
     *
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        // Extract subdomain from host
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = count($parts) > 2 ? $parts[0] : null;

        if (! $subdomain) {
            abort(404, 'Waitlist not found');
        }

        // Find the project by subdomain
        $project = Project::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->with('waitlistTemplate')
            ->first();

        if (! $project || ! $project->waitlist_template_id) {
            abort(404, 'Waitlist not found or not active');
        }

        // Get template and customizations
        $template = $project->waitlistTemplate;
        $customizations = $project->template_customizations ?: [];

        // Pass template and customizations to the Waitlist component
        return Inertia::render('Waitlist/Landing', [
            'project' => $project,
            'template' => $template,
            'customizations' => $customizations,
        ]);
    }
}
