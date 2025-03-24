<?php

use App\Models\Project;
use App\Models\WaitlistTemplate;
use App\Repositories\WaitlistTemplateRepository;

beforeEach(function () {
    $this->project = Project::factory()->create();
    $this->template = WaitlistTemplate::factory()->create();
    $this->repository = new WaitlistTemplateRepository;
});

test('waitlist template repository can set a template for a project', function () {
    $result = $this->repository->setTemplateForProject($this->project, $this->template);

    expect($result)->toBeTrue();

    // Refresh the project from database
    $this->project->refresh();

    // Check that the template is set correctly
    expect($this->project->waitlist_template_id)->toBe($this->template->id);
    expect($this->project->template_customizations)->toBeArray();
    expect($this->project->template_customizations)->toBeEmpty();
});

test('waitlist template repository can update customizations for a project', function () {
    // First set the template
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Original Heading'];
    $this->project->save();

    // Update with new data
    $data = [
        'customizations' => [
            'heading' => 'Updated Heading',
            'backgroundColor' => '#ffffff',
        ],
    ];

    $result = $this->repository->updateForProject($this->project, $this->template, $data);

    expect($result)->toBeTrue();

    // Refresh the project from database
    $this->project->refresh();

    // Check that customizations were updated
    expect($this->project->template_customizations)->toBeArray();
    expect($this->project->template_customizations)->toHaveKey('heading');
    expect($this->project->template_customizations['heading'])->toBe('Updated Heading');
    expect($this->project->template_customizations)->toHaveKey('backgroundColor');
    expect($this->project->template_customizations['backgroundColor'])->toBe('#ffffff');
});

test('waitlist template repository can remove a template from a project', function () {
    // First set the template
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Original Heading'];
    $this->project->save();

    $result = $this->repository->removeFromProject($this->project);

    expect($result)->toBeTrue();

    // Refresh the project from database
    $this->project->refresh();

    // Check that template is removed
    expect($this->project->waitlist_template_id)->toBeNull();
    expect($this->project->template_customizations)->toBeNull();
});

test('waitlist template repository can find all active templates', function () {
    // Clear existing templates to avoid test pollution
    WaitlistTemplate::query()->delete();

    // Create the template for this test
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    // Create inactive templates
    WaitlistTemplate::factory()->count(2)->create(['is_active' => false]);

    // Create active templates
    WaitlistTemplate::factory()->count(2)->create(['is_active' => true]);

    $templates = $this->repository->all();

    // Should only have 3 templates (2 created above + 1 created specifically for this test)
    expect($templates)->toHaveCount(3);

    // All should be active
    foreach ($templates as $template) {
        expect($template->is_active)->toBeTrue();
    }
});

test('updating template customizations with invalid data keeps existing customizations', function () {
    // First set the template with some customizations
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Original Heading'];
    $this->project->save();

    // Try to update with invalid data (missing customizations key)
    $data = ['some_other_data' => 'value'];

    $result = $this->repository->updateForProject($this->project, $this->template, $data);

    expect($result)->toBeTrue();

    // Refresh the project from database
    $this->project->refresh();

    // Check that customizations remain unchanged
    expect($this->project->template_customizations)->toBeArray();
    expect($this->project->template_customizations)->toHaveKey('heading');
    expect($this->project->template_customizations['heading'])->toBe('Original Heading');
});
