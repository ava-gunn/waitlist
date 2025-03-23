<?php

use App\Models\Project;
use App\Models\Signup;
use App\Repositories\SignupRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

test('signup repository can create a signup', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();
    $repository = new SignupRepository;

    $data = [
        'project_id' => $project->id,
        'email' => 'test@example.com',
        'name' => 'Test User',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Mozilla/5.0',
        'referrer' => 'https://example.com',
        'verification_token' => 'token123',
    ];

    $signup = $repository->create($data);

    expect($signup)->toBeInstanceOf(Signup::class);
    expect($signup->project_id)->toBe($project->id);
    expect($signup->email)->toBe('test@example.com');
    expect($signup->verification_token)->toBe('token123');

    // Verify database was updated by querying it directly
    $dbSignup = DB::table('signups')
        ->where('email', 'test@example.com')
        ->where('name', 'Test User')
        ->first();

    expect($dbSignup)->not->toBeNull();
});

test('signup repository can update a signup', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $signup = Signup::factory()->create([
        'name' => 'Original Name',
        'verified_at' => null,
    ]);

    $repository = new SignupRepository;

    $data = [
        'name' => 'Updated Name',
        'verified_at' => now(),
    ];

    $updatedSignup = $repository->update($signup, $data);

    expect($updatedSignup->name)->toBe('Updated Name');
    expect($updatedSignup->verified_at)->not->toBeNull();

    // Verify the database was updated by querying it directly
    $dbSignup = DB::table('signups')
        ->where('id', $signup->id)
        ->where('name', 'Updated Name')
        ->first();

    expect($dbSignup)->not->toBeNull();
});

test('signup repository can delete a signup', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $signup = Signup::factory()->create();

    $repository = new SignupRepository;
    $result = $repository->delete($signup);

    expect($result)->toBeTrue();

    // Verify the database record was deleted by querying it directly
    $dbSignup = DB::table('signups')
        ->where('id', $signup->id)
        ->first();

    expect($dbSignup)->toBeNull();
});

test('signup repository can verify an email', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $token = 'verification_token_123';
    $signup = Signup::factory()->create([
        'verification_token' => $token,
        'verified_at' => null,
    ]);

    $repository = new SignupRepository;
    $verifiedSignup = $repository->verifyEmail($token);

    expect($verifiedSignup->id)->toBe($signup->id);
    expect($verifiedSignup->verified_at)->not->toBeNull();

    // Verify the database was updated by querying it directly
    $dbSignup = DB::table('signups')
        ->where('id', $signup->id)
        ->first();

    expect($dbSignup)->not->toBeNull();
    expect($dbSignup->verification_token)->toBeNull();
});

test('signup repository returns null when verifying with invalid token', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    // Create a signup with a known token
    Signup::factory()->create([
        'verification_token' => 'known_token',
        'verified_at' => null,
    ]);

    $repository = new SignupRepository;
    $result = $repository->verifyEmail('invalid_token');

    expect($result)->toBeNull();
});

test('signup repository can get signups by project with filters', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();

    // Create verified signups
    Signup::factory()->count(3)->create([
        'project_id' => $project->id,
        'verified_at' => now(),
    ]);

    // Create unverified signups
    Signup::factory()->count(2)->create([
        'project_id' => $project->id,
        'verified_at' => null,
    ]);

    $repository = new SignupRepository;

    // Test filtering by verified
    $verifiedSignups = $repository->getSignupsByProject($project->id, ['verified' => true]);
    expect($verifiedSignups)->toHaveCount(3);

    // Test filtering by unverified
    $unverifiedSignups = $repository->getSignupsByProject($project->id, ['verified' => false]);
    expect($unverifiedSignups)->toHaveCount(2);

    // Test with search filter
    Signup::factory()->create([
        'project_id' => $project->id,
        'email' => 'searchtest@example.com',
    ]);

    $searchResults = $repository->getSignupsByProject($project->id, ['search' => 'searchtest']);
    expect($searchResults)->toHaveCount(1);
    expect($searchResults->first()->email)->toBe('searchtest@example.com');
});

test('signup repository can get signups count by date', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();

    // Create signups on different dates
    $today = now();
    $yesterday = now()->subDay();
    $dayBefore = now()->subDays(2);

    // Today's signups
    Signup::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_at' => $today,
    ]);

    // Yesterday's signups
    Signup::factory()->count(2)->create([
        'project_id' => $project->id,
        'created_at' => $yesterday,
    ]);

    // Day before signups
    Signup::factory()->count(1)->create([
        'project_id' => $project->id,
        'created_at' => $dayBefore,
    ]);

    $repository = new SignupRepository;
    $counts = $repository->getSignupsCountByDate($project->id, 3);

    expect($counts)->toBeArray();
    expect(count($counts))->toBe(3);
    expect($counts[$today->format('Y-m-d')] ?? 0)->toBe(3);
    expect($counts[$yesterday->format('Y-m-d')] ?? 0)->toBe(2);
    expect($counts[$dayBefore->format('Y-m-d')] ?? 0)->toBe(1);
});
