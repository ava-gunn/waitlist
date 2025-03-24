<?php

use App\Models\Project;
use App\Models\WaitlistTemplate;

test('waitlist template has correct fillable attributes', function () {
    $fillable = ['name', 'description', 'thumbnail', 'structure', 'is_active'];
    expect((new WaitlistTemplate)->getFillable())->toBe($fillable);
});

test('waitlist template has many projects', function () {
    $template = WaitlistTemplate::factory()->create();
    $project = Project::factory()->create([
        'waitlist_template_id' => $template->id,
        'template_customizations' => ['heading' => 'Custom Heading'],
    ]);

    expect($template->projects)->toHaveCount(1);
    expect($template->projects->first())->toBeInstanceOf(Project::class);
    expect($template->projects->first()->waitlist_template_id)->toBe($template->id);
    expect($template->projects->first()->template_customizations)->toBeArray();
    expect($template->projects->first()->template_customizations)->toHaveKey('heading');
    expect($template->projects->first()->template_customizations['heading'])->toBe('Custom Heading');
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

    expect($template->structure)->toBe($structure);
    expect($template->structure)->toBeArray();
});

test('active scope returns only active templates', function () {
    // Clear existing templates to avoid test pollution
    WaitlistTemplate::query()->delete();

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
