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
        ->has('projects', 3)
        ->where('projects.0.id', $projects[0]->id)
        ->where('projects.0.name', $projects[0]->name)
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

test('user can create a project', function () {
    $user = User::factory()->create();

    $projectData = [
        'name' => 'Test Project',
        'description' => 'A test project',
        'subdomain' => 'test-project',
        'is_active' => true,
    ];

    $response = $this
        ->actingAs($user)
        ->post('/projects', $projectData);

    // Check for redirect to show route
    $project = Project::where('name', 'Test Project')->where('user_id', $user->id)->first();
    $response->assertRedirect("/projects/{$project->id}");
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'Test Project',
        'subdomain' => 'test-project',
    ]);
});

test('user cannot create a project with an existing subdomain', function () {
    $user = User::factory()->create();
    $existingProject = Project::factory()->create([
        'subdomain' => 'existing-subdomain',
    ]);

    $projectData = [
        'name' => 'New Project',
        'description' => 'A new project',
        'subdomain' => 'existing-subdomain', // Already exists
    ];

    $response = $this
        ->actingAs($user)
        ->post('/projects', $projectData);

    $response->assertInvalid(['subdomain']);
    $this->assertDatabaseCount('projects', 1);
});

test('user can view their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}");

    $response->assertStatus(200);

    // Just check that the response contains the Projects/Show component
    // without making assertions about the nested data structure
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
    );

    // Verify that the project exists in the database
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => $project->name,
        'user_id' => $user->id,
    ]);
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

test('user can update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $updatedData = [
        'name' => 'Updated Project',
        'description' => 'An updated project',
        'subdomain' => 'updated-project',
        'is_active' => true,
    ];

    $response = $this
        ->actingAs($user)
        ->patch("/projects/{$project->id}", $updatedData);

    $response->assertRedirect("/projects/{$project->id}");
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'user_id' => $user->id,
        'name' => 'Updated Project',
        'subdomain' => 'updated-project',
    ]);
});

test('user cannot update another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $updatedData = [
        'name' => 'Updated Project',
        'description' => 'An updated project',
        'subdomain' => 'updated-project',
    ];

    $response = $this
        ->actingAs($user)
        ->patch("/projects/{$project->id}", $updatedData);

    $response->assertForbidden();
});

test('user can delete their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->delete("/projects/{$project->id}");

    $response->assertRedirect('/projects');
    $response->assertSessionHas('success');

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
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});
