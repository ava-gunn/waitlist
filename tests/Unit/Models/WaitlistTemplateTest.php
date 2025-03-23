<?php

use App\Models\Project;
use App\Models\WaitlistTemplate;
use Illuminate\Support\Facades\Artisan;

test('waitlist template has correct fillable attributes', function () {
    $fillable = ['name', 'description', 'thumbnail', 'structure', 'is_active'];
    expect((new WaitlistTemplate)->getFillable())->toBe($fillable);
});

test('waitlist template belongs to many projects', function () {
    $template = WaitlistTemplate::factory()->create();
    $project = Project::factory()->create();

    $template->projects()->attach($project->id, [
        'is_active' => true,
        'customizations' => json_encode(['heading' => 'Custom Heading']),
    ]);

    expect($template->projects)->toHaveCount(1);
    expect($template->projects->first())->toBeInstanceOf(Project::class);
    expect($template->projects->first()->pivot->is_active)->toBeTrue();
    expect(json_decode($template->projects->first()->pivot->customizations))->toHaveProperty('heading', 'Custom Heading');
});

test('waitlist template structure is cast to array', function () {
    $structure = [
        'settings' => [
            'backgroundColor' => '#ffffff',
            'textColor' => '#000000',
        ],
        'components' => [
            ['type' => 'header', 'content' => 'Join Our Waitlist'],
            ['type' => 'text', 'content' => 'Be the first to know when we launch.'],
            ['type' => 'form', 'button' => ['text' => 'Sign Up']],
        ],
    ];

    $template = WaitlistTemplate::factory()->create([
        'structure' => $structure,
    ]);

    expect($template->structure)->toBeArray();
    expect($template->structure)->toHaveKey('settings');
    expect($template->structure)->toHaveKey('components');
    expect($template->structure['settings']['backgroundColor'])->toBe('#ffffff');
    expect($template->structure['components'][0]['type'])->toBe('header');
});

test('active scope returns only active templates', function () {
    // Clear the database first
    Artisan::call('migrate:fresh');

    // Create inactive templates
    WaitlistTemplate::factory()->count(2)->create(['is_active' => false]);

    // Create active templates
    WaitlistTemplate::factory()->count(3)->create(['is_active' => true]);

    $activeTemplates = WaitlistTemplate::active()->get();

    expect($activeTemplates)->toHaveCount(3);
    $activeTemplates->each(function ($template) {
        expect($template->is_active)->toBeTrue();
    });
});
