<?php

use App\Models\Project;
use App\Models\Signup;
use App\Models\User;
use App\Models\WaitlistTemplate;

test('project has correct fillable attributes', function () {
    $fillable = ['name', 'description', 'subdomain', 'settings', 'logo_path', 'is_active', 'user_id'];
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

test('project belongs to many waitlist templates', function () {
    $project = Project::factory()->create();
    $template = WaitlistTemplate::factory()->create();

    $project->waitlistTemplates()->attach($template->id, [
        'is_active' => true,
        'customizations' => json_encode(['heading' => 'Custom Heading']),
    ]);

    expect($project->waitlistTemplates)->toHaveCount(1);
    expect($project->waitlistTemplates->first())->toBeInstanceOf(WaitlistTemplate::class);
    expect($project->waitlistTemplates->first()->pivot->is_active)->toBeTrue();
    expect(json_decode($project->waitlistTemplates->first()->pivot->customizations))->toHaveProperty('heading', 'Custom Heading');
});

test('project can get active template', function () {
    $project = Project::factory()->create();
    $template1 = WaitlistTemplate::factory()->create(['name' => 'Template 1']);
    $template2 = WaitlistTemplate::factory()->create(['name' => 'Template 2']);

    // Attach with first template inactive
    $project->waitlistTemplates()->attach($template1->id, ['is_active' => false]);

    // Attach with second template active
    $project->waitlistTemplates()->attach($template2->id, ['is_active' => true]);

    expect($project->activeTemplate)->toBeInstanceOf(WaitlistTemplate::class);
    expect($project->activeTemplate->name)->toBe('Template 2');
});

test('project full url is correctly generated', function () {
    $project = Project::factory()->create(['subdomain' => 'test-project']);

    // This assumes your application sets APP_URL in the environment
    $appUrl = config('app.url');
    $domain = parse_url($appUrl, PHP_URL_HOST);
    $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';

    $expectedUrl = "{$scheme}://test-project.{$domain}";

    expect($project->full_url)->toBe($expectedUrl);
});

test('project can determine if it has signups', function () {
    $project = Project::factory()->create();

    expect($project->hasSignups())->toBeFalse();

    Signup::factory()->create(['project_id' => $project->id]);
    $project->refresh();

    expect($project->hasSignups())->toBeTrue();
});
