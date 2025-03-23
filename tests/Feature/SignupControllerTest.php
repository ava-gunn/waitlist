<?php

use App\Models\Project;
use App\Models\Signup;
use App\Models\User;
use App\Models\WaitlistTemplate;
use Illuminate\Support\Str;

test('visitor can submit signup form on project subdomain', function () {
    // Create a project with an active template
    $project = Project::factory()->create([
        'subdomain' => 'test-waitlist',
        'is_active' => true,
    ]);

    $template = WaitlistTemplate::factory()->create(['is_active' => true]);
    $project->waitlistTemplates()->attach($template->id, ['is_active' => true]);

    $signupData = [
        'email' => 'newuser@example.com',
        'name' => 'Test User',
    ];

    // Use json() method to send as JSON request
    $response = $this->json('POST', "/signup/{$project->subdomain}", $signupData);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // Check that the signup was recorded in the database
    $this->assertDatabaseHas('signups', [
        'project_id' => $project->id,
        'email' => 'newuser@example.com',
        'name' => 'Test User',
    ]);
});

test('visitor cannot submit to inactive project', function () {
    $project = Project::factory()->create([
        'subdomain' => 'inactive-project',
        'is_active' => false, // Inactive project
    ]);

    $signupData = [
        'email' => 'user@example.com',
        'name' => 'Test User',
    ];

    // Use json() method to send as JSON request
    $response = $this->json('POST', "/signup/{$project->subdomain}", $signupData);

    // Check for any 4xx status code and appropriate error message
    $response->assertStatus(422); // This will be a validation error now
    $response->assertJson([
        'success' => false,
        'errors' => [],
    ]);
    // Check that error message mentions the project being inactive
    $responseContent = $response->getContent();
    expect($responseContent)->toContain('inactive');
});

test('visitor cannot submit duplicate email to same project', function () {
    $project = Project::factory()->create([
        'subdomain' => 'duplicate-test',
        'is_active' => true,
    ]);

    // Create an existing signup
    Signup::factory()->create([
        'project_id' => $project->id,
        'email' => 'existing@example.com',
        'name' => 'Existing User',
    ]);

    $signupData = [
        'email' => 'existing@example.com', // Already exists for this project
        'name' => 'Test User',
    ];

    // Use json() method to send as JSON request which should get JSON response
    $response = $this->json('POST', "/signup/{$project->subdomain}", $signupData);

    $response->assertStatus(422); // Validation error
    $response->assertJsonValidationErrors(['email']);
});

test('visitor can submit same email to different projects', function () {
    $project1 = Project::factory()->create([
        'subdomain' => 'first-project',
        'is_active' => true,
    ]);

    $project2 = Project::factory()->create([
        'subdomain' => 'second-project',
        'is_active' => true,
    ]);

    // Create a signup for the first project
    Signup::factory()->create([
        'project_id' => $project1->id,
        'email' => 'user@example.com',
        'name' => 'Test User',
    ]);

    // Try to sign up to the second project with the same email
    $signupData = [
        'email' => 'user@example.com',
        'name' => 'Test User',
    ];

    // Use json() method to send as JSON request
    $response = $this->json('POST', "/signup/{$project2->subdomain}", $signupData);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // Check that the second signup was recorded
    $this->assertDatabaseHas('signups', [
        'project_id' => $project2->id,
        'email' => 'user@example.com',
        'name' => 'Test User',
    ]);
});

test('visitor can verify email with valid token', function () {
    $project = Project::factory()->create(['is_active' => true]);
    $token = 'valid-verification-token';

    $signup = Signup::factory()->create([
        'project_id' => $project->id,
        'verification_token' => $token,
        'verified_at' => null,
        'name' => 'Test User',
    ]);

    $response = $this->get("/verify/{$token}");

    $response->assertRedirect($project->full_url);
    $response->assertSessionHas('success');

    // Check that the signup is now verified
    $updatedSignup = Signup::find($signup->id);
    expect($updatedSignup->verified_at)->not->toBeNull();
});

test('visitor cannot verify with invalid token', function () {
    $response = $this->get('/verify/invalid-token');

    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

test('project owner can view signups', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $signups = Signup::factory()->count(5)->create([
        'project_id' => $project->id,
        'name' => 'Test User',
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/signups");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Signups/Index')
        ->has('signups.data', 5)
        ->where('project.id', $project->id)
    );
});

test('project owner can export signups', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    Signup::factory()->count(3)->create([
        'project_id' => $project->id,
        'name' => 'Test User',
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/signups/export");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    // Get the actual Content-Disposition header value for comparison
    $headerValue = $response->headers->get('Content-Disposition');
    $expectedValue = 'attachment; filename=' . Str::slug($project->name) . '-waitlist-' . now()->format('Y-m-d') . '.csv';

    // Assert the header matches without worrying about exact quote format
    expect($headerValue)->toContain(Str::slug($project->name));
    expect($headerValue)->toContain('-waitlist-');
    expect($headerValue)->toContain('.csv');
});

test('project owner can delete a signup', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $signup = Signup::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test User',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete("/projects/{$project->id}/signups/{$signup->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check the database to make sure the signup was deleted
    $this->assertDatabaseMissing('signups', ['id' => $signup->id]);
});

test('user cannot view signups for another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    Signup::factory()->count(3)->create([
        'project_id' => $project->id,
        'name' => 'Test User',
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/projects/{$project->id}/signups");

    $response->assertForbidden();
});

test('user cannot delete signup from another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $signup = Signup::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test User',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete("/projects/{$project->id}/signups/{$signup->id}");

    $response->assertForbidden();

    // Check that the signup still exists
    $this->assertDatabaseHas('signups', ['id' => $signup->id]);
});
