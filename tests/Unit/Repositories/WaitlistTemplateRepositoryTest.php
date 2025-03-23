<?php

use App\Models\Project;
use App\Models\WaitlistTemplate;
use App\Repositories\WaitlistTemplateRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

test('waitlist template repository can activate a template for a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();
    $repository = new WaitlistTemplateRepository;

    $result = $repository->activateForProject($project, $template);

    expect($result)->toBeTrue();

    // Check pivot table has the correct data
    $pivotData = DB::table('project_waitlist_template')
        ->where('project_id', $project->id)
        ->where('waitlist_template_id', $template->id)
        ->first();

    expect($pivotData)->not->toBeNull();
    expect($pivotData->is_active)->toBe(1); // SQLite returns 1 for true
});

test('waitlist template repository can update customizations for a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();
    $repository = new WaitlistTemplateRepository;

    // Attach the template first
    $project->waitlistTemplates()->attach($template->id, [
        'is_active' => true,
        'customizations' => json_encode(['heading' => 'Original Heading']),
    ]);

    // Update with new data
    $data = [
        'customizations' => [
            'heading' => 'Updated Heading',
            'backgroundColor' => '#ffffff',
        ],
        'is_active' => true,
    ];

    $result = $repository->updateForProject($project, $template, $data);
    expect($result)->toBeTrue();

    // Check the database has the updated data
    $pivotData = DB::table('project_waitlist_template')
        ->where('project_id', $project->id)
        ->where('waitlist_template_id', $template->id)
        ->first();

    expect($pivotData)->not->toBeNull();

    // Get the JSON content and check key values
    $customizationsJson = $pivotData->customizations;
    expect($customizationsJson)->toContain('Updated Heading');
    expect($customizationsJson)->toContain('#ffffff');

    // Simplify the JSON checks - only check the string contains expected values
    expect($pivotData->is_active)->toBe(1); // SQLite returns 1 for true
});

test('waitlist template repository can deactivate a template for a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();
    $repository = new WaitlistTemplateRepository;

    // Attach the template first with active status
    $project->waitlistTemplates()->attach($template->id, [
        'is_active' => true,
    ]);

    $result = $repository->deactivateForProject($project, $template);

    expect($result)->toBeTrue();

    // Check the database has the updated status
    $pivotData = DB::table('project_waitlist_template')
        ->where('project_id', $project->id)
        ->where('waitlist_template_id', $template->id)
        ->first();

    expect($pivotData)->not->toBeNull();
    expect($pivotData->is_active)->toBe(0); // SQLite returns 0 for false
});

test('waitlist template repository can deactivate all templates for a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template1 = WaitlistTemplate::factory()->create();
    $template2 = WaitlistTemplate::factory()->create();
    $template3 = WaitlistTemplate::factory()->create();
    $repository = new WaitlistTemplateRepository;

    // Attach multiple templates with active status
    $project->waitlistTemplates()->attach([
        $template1->id => ['is_active' => true],
        $template2->id => ['is_active' => true],
        $template3->id => ['is_active' => true],
    ]);

    // Force a refresh to ensure we have the latest data
    $project->refresh();

    // Call the deactivation method
    $result = $repository->deactivateAllForProject($project);
    expect($result)->toBeTrue();

    // Check all templates are now inactive by querying the database directly
    $pivotData = DB::table('project_waitlist_template')
        ->where('project_id', $project->id)
        ->get();

    expect($pivotData)->toHaveCount(3);
    foreach ($pivotData as $item) {
        expect($item->is_active)->toBe(0); // SQLite returns 0 for false
    }
});

test('waitlist template repository can find all active templates', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    // Create inactive templates
    WaitlistTemplate::factory()->count(2)->create(['is_active' => false]);

    // Create active templates
    WaitlistTemplate::factory()->count(3)->create(['is_active' => true]);

    $repository = new WaitlistTemplateRepository;
    $activeTemplates = $repository->findAllActive();

    expect($activeTemplates)->toHaveCount(3);
    $activeTemplates->each(function ($template) {
        expect($template->is_active)->toBeTrue();
    });
});

test('waitlist template repository can find active template for a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template1 = WaitlistTemplate::factory()->create(['name' => 'Template 1']);
    $template2 = WaitlistTemplate::factory()->create(['name' => 'Template 2']);
    $repository = new WaitlistTemplateRepository;

    // Attach with first template inactive
    $project->waitlistTemplates()->attach($template1->id, ['is_active' => false]);

    // Attach with second template active
    $project->waitlistTemplates()->attach($template2->id, ['is_active' => true]);

    $activeTemplate = $repository->findActiveForProject($project);

    expect($activeTemplate)->toBeInstanceOf(WaitlistTemplate::class);
    expect($activeTemplate->name)->toBe('Template 2');
});

test('waitlist template repository returns null when no active template for project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();
    $repository = new WaitlistTemplateRepository;

    // Attach with template inactive
    $project->waitlistTemplates()->attach($template->id, ['is_active' => false]);

    $activeTemplate = $repository->findActiveForProject($project);

    expect($activeTemplate)->toBeNull();
});
