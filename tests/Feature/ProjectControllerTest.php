<?php

use App\Models\Project;
use App\Models\User;

test('user can view their projects', function () {
    $user = User::factory()->create();
    $projects = Project::factory()->count(3)->create(['user_id' => $user->id]);

    // Create another user's projects (should not appear in response)
    Project::factory()->count(2)->create();

    $response = $this
        ->actingAs($user)
        ->get('/projects');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Index')
        ->has('projects.data', 3)
        ->where('projects.data.0.id', $projects[0]->id)
        ->where('projects.data.0.name', $projects[0]->name)
    );
});

test('user can view project creation page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/projects/create');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Create')
    );
});

test('user can create a new project', function () {
    $user = User::factory()->create();

    $projectData = [
        'name' => 'New Test Project',
        'description' => 'A test project description',
        'subdomain' => 'new-test-project',
        'settings' => [
            'collect_name' => true,
            'social_sharing' => true,
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->post('/projects', $projectData);

    // Check response
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check database
    $this->assertDatabaseHas('projects', [
        'name' => 'New Test Project',
        'description' => 'A test project description',
        'subdomain' => 'new-test-project',
        'user_id' => $user->id,
    ]);

    // Verify the settings are stored as JSON
    $project = Project::where('name', 'New Test Project')->first();
    expect($project->settings)->toBeArray();
    expect($project->settings)->toHaveKey('collect_name', true);
    expect($project->settings)->toHaveKey('social_sharing', true);
});

test('user cannot create a project with an existing subdomain', function () {
    $user = User::factory()->create();

    // Create a project with a specific subdomain
    Project::factory()->create([
        'subdomain' => 'existing-subdomain',
    ]);

    $projectData = [
        'name' => 'New Project',
        'description' => 'A new project description',
        'subdomain' => 'existing-subdomain', // Already exists
    ];

    $response = $this
        ->actingAs($user)
        ->post('/projects', $projectData);

    $response->assertSessionHasErrors(['subdomain']);

    // Check that no new project was created
    $this->assertDatabaseCount('projects', 1);
});

test('user can view a specific project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
        ->has('project')
        ->where('project.data.id', $project->id)
        ->where('project.data.name', $project->name)
    );
});

test('user cannot view another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}");

    $response->assertForbidden();
});

test('user can edit their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Edit')
        ->has('project')
        ->where('project.data.id', $project->id)
    );
});

test('user can update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $updatedData = [
        'name' => 'Updated Project Name',
        'description' => 'Updated project description',
        'subdomain' => $project->subdomain, // Include the existing subdomain
        'settings' => [
            'collect_name' => true,
            'social_sharing' => false,
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->patch("/projects/{$project->id}", $updatedData);

    // Check response
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check database
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project Name',
        'description' => 'Updated project description',
    ]);

    // Verify the settings are updated
    $updatedProject = Project::find($project->id);
    expect($updatedProject->settings)->toBeArray();
    expect($updatedProject->settings)->toHaveKey('collect_name', true);
    expect($updatedProject->settings)->toHaveKey('social_sharing', false);
});

test('user can delete their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->delete("/projects/{$project->id}");

    // Check response
    $response->assertRedirect('/projects');
    $response->assertSessionHas('success');

    // Check database
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('user cannot delete another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->delete("/projects/{$project->id}");

    $response->assertForbidden();

    // Check database to make sure the project still exists
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});
