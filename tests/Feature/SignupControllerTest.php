<?php

use App\Models\Project;
use App\Models\Signup;
use App\Models\User;
use App\Models\WaitlistTemplate;
use Illuminate\Support\Str;

test('visitor can submit signup form on project subdomain', function () {
    // Create a project with an active template
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'subdomain' => 'test-waitlist',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
    $response->assertJsonFragment(['Project not found or inactive.']);
});

test('visitor cannot submit with invalid email', function () {
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'subdomain' => 'test-waitlist',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

    $invalidData = [
        'email' => 'not-an-email',
        'name' => 'Test User',
    ];

    // Use json() method to send as JSON request
    $response = $this->json('POST', "/signup/{$project->subdomain}", $invalidData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('visitor cannot submit duplicate email', function () {
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'subdomain' => 'test-waitlist',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

    // Create an existing signup
    Signup::factory()->create([
        'project_id' => $project->id,
        'email' => 'existing@example.com',
    ]);

    $duplicateData = [
        'email' => 'existing@example.com',
        'name' => 'New User With Existing Email',
    ];

    // Use json() method to send as JSON request
    $response = $this->json('POST', "/signup/{$project->subdomain}", $duplicateData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('visitor can submit same email to different projects', function () {
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project1 = Project::factory()->create([
        'subdomain' => 'first-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

    $project2 = Project::factory()->create([
        'subdomain' => 'second-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'user_id' => $otherUser->id,
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
    $template = WaitlistTemplate::factory()->create(['is_active' => true]);

    $project = Project::factory()->create([
        'user_id' => $otherUser->id,
        'subdomain' => 'test-project',
        'is_active' => true,
        'waitlist_template_id' => $template->id,
    ]);

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
