<?php

use App\Models\Project;
use App\Models\User;
use App\Models\WaitlistTemplate;

test('user can view templates for their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $templates = WaitlistTemplate::factory()->count(3)->create(['is_active' => true]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/templates");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Templates/Index')
        ->has('templates', 3)
        ->where('project.id', $project->id)
    );
});

test('user cannot view templates for another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    WaitlistTemplate::factory()->count(3)->create(['is_active' => true]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/templates");

    $response->assertForbidden();
});

test('user can view template customization page', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/templates/{$template->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Templates/Edit')
        ->where('project.id', $project->id)
        ->where('template.id', $template->id)
    );
});

test('user can set a template for their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $response = $this
        ->actingAs($user)
        ->post("/projects/{$project->id}/templates/{$template->id}/set");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh the project from database
    $project->refresh();

    // Check that the template is set
    expect($project->waitlist_template_id)->toBe($template->id);
});

test('user can customize a template for their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $template = WaitlistTemplate::factory()->create();

    // Set the template for the project
    $project->waitlist_template_id = $template->id;
    $project->template_customizations = [];
    $project->save();

    // Data to customize the template
    $customizationData = [
        'customizations' => [
            'heading' => 'Custom Heading',
            'description' => 'Custom description text',
            'buttonText' => 'Join Now',
            'backgroundColor' => '#f5f5f5',
            'textColor' => '#333333',
            'buttonColor' => '#4c51bf',
            'buttonTextColor' => '#ffffff',
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->patch("/projects/{$project->id}/templates/{$template->id}", $customizationData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh the project from database
    $project->refresh();

    // Check that the customizations were updated
    expect($project->template_customizations)->toBeArray();
    expect($project->template_customizations)->toHaveKey('heading');
    expect($project->template_customizations['heading'])->toBe('Custom Heading');
});

test('user can remove a template from their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    // First set the template
    $project->waitlist_template_id = $template->id;
    $project->save();

    $response = $this
        ->actingAs($user)
        ->post("/projects/{$project->id}/templates/remove");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Refresh the project from database
    $project->refresh();

    // Check that the template was removed
    expect($project->waitlist_template_id)->toBeNull();
});

test('user cannot customize template for another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    // Set the template for the project
    $project->waitlist_template_id = $template->id;
    $project->save();

    $customizationData = [
        'customizations' => [
            'heading' => 'Custom Heading',
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->patch("/projects/{$project->id}/templates/{$template->id}", $customizationData);

    $response->assertForbidden();
});
