<?php

use App\Http\Resources\WaitlistTemplateResource;
use App\Models\WaitlistTemplate;

test('waitlist template resource properly handles array data in structure field', function () {
    // Create a template with structure already as array (as it would be from model casting)
    $template = WaitlistTemplate::factory()->make([
        'id' => 1,
        'name' => 'Test Template',
        'description' => 'Template description',
        'structure' => ['layout' => 'default', 'components' => ['header', 'form']],
        'default_customizations' => ['color' => '#000000'],
        'is_active' => true,
    ]);

    $resource = new WaitlistTemplateResource($template);
    $array = $resource->toArray(request());

    // Assert that the transformation works correctly with array data
    expect($array)->toBeArray();
    expect($array['structure'])->toBeArray();
    expect($array['structure']['layout'])->toBe('default');
    expect($array['default_customizations'])->toBeArray();
    expect($array['default_customizations']['color'])->toBe('#000000');
});

test('waitlist template resource properly handles string data in structure field', function () {
    // Create a template with structure as JSON string (as it might be before model casting)
    $template = WaitlistTemplate::factory()->make([
        'id' => 1,
        'name' => 'Test Template',
        'description' => 'Template description',
        'structure' => json_encode(['layout' => 'default', 'components' => ['header', 'form']]),
        'default_customizations' => json_encode(['color' => '#000000']),
        'is_active' => true,
    ]);

    // Manually bypass the model casting for testing
    $template->timestamps = false;
    $template->casts = [];

    $resource = new WaitlistTemplateResource($template);
    $array = $resource->toArray(request());

    // Assert that the transformation works correctly with string data
    expect($array)->toBeArray();
    expect($array['structure'])->toBeArray();
    expect($array['structure']['layout'])->toBe('default');
    expect($array['default_customizations'])->toBeArray();
    expect($array['default_customizations']['color'])->toBe('#000000');
});

test('waitlist template resource properly handles null values', function () {
    // Create a template with null values
    $template = WaitlistTemplate::factory()->make([
        'id' => 1,
        'name' => 'Test Template',
        'description' => 'Template description',
        'structure' => null,
        'default_customizations' => null,
        'is_active' => true,
    ]);

    $resource = new WaitlistTemplateResource($template);
    $array = $resource->toArray(request());

    // Assert that null values become empty arrays
    expect($array)->toBeArray();
    expect($array['structure'])->toBeArray();
    expect($array['structure'])->toBeEmpty();
    expect($array['default_customizations'])->toBeArray();
    expect($array['default_customizations'])->toBeEmpty();
});
