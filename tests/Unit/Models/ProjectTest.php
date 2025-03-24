<?php

use App\Models\Project;
use App\Models\Signup;
use App\Models\User;
use App\Models\WaitlistTemplate;

test('project has correct fillable attributes', function () {
    $fillable = ['name', 'description', 'subdomain', 'settings', 'logo_path', 'is_active', 'user_id', 'waitlist_template_id', 'template_customizations'];
    expect((new Project)->getFillable())->toBe($fillable);
});

test('project belongs to a user', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    expect($project->user)->toBeInstanceOf(User::class);
    expect($project->user->id)->toBe($user->id);
});

test('project has many signups', function () {
    $project = Project::factory()->create();
    Signup::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->signups)->toHaveCount(3);
    expect($project->signups->first())->toBeInstanceOf(Signup::class);
});

test('project belongs to a waitlist template', function () {
    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();

    $project->waitlist_template_id = $template->id;
    $project->template_customizations = ['heading' => 'Custom Heading'];
    $project->save();

    $project->refresh();

    expect($project->waitlistTemplate)->toBeInstanceOf(WaitlistTemplate::class);
    expect($project->waitlistTemplate->id)->toBe($template->id);
    expect($project->template_customizations)->toBeArray();
    expect($project->template_customizations)->toHaveKey('heading');
    expect($project->template_customizations['heading'])->toBe('Custom Heading');
});

test('project with no template returns null for waitlistTemplate relation', function () {
    $project = Project::factory()->create([
        'waitlist_template_id' => null,
    ]);

    expect($project->waitlistTemplate)->toBeNull();
});

test('project full url is correctly generated', function () {
    $project = Project::factory()->create([
        'subdomain' => 'test-project',
    ]);

    // Using a test environment, so localhost
    expect($project->full_url)->toContain('test-project');
});

test('project can determine if it has signups', function () {
    $project = Project::factory()->create();
    expect($project->hasSignups())->toBeFalse();

    Signup::factory()->create(['project_id' => $project->id]);
    expect($project->hasSignups())->toBeTrue();
});
