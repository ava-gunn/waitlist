<?php

use App\Models\Project;
use App\Models\User;
use App\Models\WaitlistTemplate;

beforeEach(function () {
    // Create a test user with a project and template for each test
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->template = WaitlistTemplate::factory()->create(['is_active' => true]);
});

test('user can view templates for their project', function () {
    WaitlistTemplate::factory()->count(2)->create(['is_active' => true]);

    $response = $this
        ->actingAs($this->user)
        ->get("/projects/{$this->project->id}/templates");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Templates/Index')
        ->has('templates', 3) // 1 from beforeEach + 2 created here
        ->where('project.id', $this->project->id)
    );
});

test('user can set a template for their project', function () {
    $response = $this
        ->actingAs($this->user)
        ->post("/projects/{$this->project->id}/templates/{$this->template->id}/set");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh model to get updated data
    $this->project->refresh();

    // Assert that the template is set
    expect($this->project->waitlist_template_id)->toBe($this->template->id);
    expect($this->project->template_customizations)->toBeArray();
    expect($this->project->template_customizations)->toBeEmpty();
});

test('user can update template customizations for their project', function () {
    // First set the template
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = [];
    $this->project->save();

    $customizationData = [
        'customizations' => [
            'heading' => 'Custom Heading',
            'description' => 'Custom description text',
            'buttonText' => 'Join Now',
            'backgroundColor' => '#f5f5f5',
            'textColor' => '#333333',
        ],
    ];

    $response = $this
        ->actingAs($this->user)
        ->patch("/projects/{$this->project->id}/templates/{$this->template->id}", $customizationData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh model to get updated data
    $this->project->refresh();

    // Assert customizations are updated
    expect($this->project->template_customizations)->toBeArray();
    expect($this->project->template_customizations)->toHaveKey('heading');
    expect($this->project->template_customizations['heading'])->toBe('Custom Heading');
    expect($this->project->template_customizations['description'])->toBe('Custom description text');
});

test('user can remove a template from their project', function () {
    // First set the template
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Test Heading'];
    $this->project->save();

    $response = $this
        ->actingAs($this->user)
        ->post("/projects/{$this->project->id}/templates/remove");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh model to get updated data
    $this->project->refresh();

    // Assert template is removed
    expect($this->project->waitlist_template_id)->toBeNull();
    expect($this->project->template_customizations)->toBeNull();
});

test('project shows template in resource when loaded', function () {
    // Set the template and customizations
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Test Resource Heading'];
    $this->project->save();

    $response = $this
        ->actingAs($this->user)
        ->get("/projects/{$this->project->id}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
        ->where('project.id', $this->project->id)
        ->where('project.waitlist_template_id', $this->template->id)
        ->where('project.template_customizations.heading', 'Test Resource Heading')
    );
});

test('user cannot set template for another users project', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($this->user)
        ->post("/projects/{$otherProject->id}/templates/{$this->template->id}/set");

    $response->assertForbidden();

    // Verify template was not set
    $otherProject->refresh();
    expect($otherProject->waitlist_template_id)->toBeNull();
});

test('edit template page shows template customizations', function () {
    // Set the template and customizations
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = ['heading' => 'Customized Heading'];
    $this->project->save();

    $response = $this
        ->actingAs($this->user)
        ->get("/projects/{$this->project->id}/templates/{$this->template->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Templates/Edit')
        ->where('project.id', $this->project->id)
        ->where('project.template_customizations.heading', 'Customized Heading')
        ->where('template.id', $this->template->id)
    );
});

test('project with null template_customizations gets empty array', function () {
    // Set the template with null customizations
    $this->project->waitlist_template_id = $this->template->id;
    $this->project->template_customizations = null;
    $this->project->save();

    $response = $this
        ->actingAs($this->user)
        ->get("/projects/{$this->project->id}/templates/{$this->template->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Templates/Edit')
        ->where('project.template_customizations', [])
    );
});
