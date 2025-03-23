<?php

use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

test('project repository can create a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $user = User::factory()->create();
    $repository = new ProjectRepository;

    $data = [
        'user_id' => $user->id,
        'name' => 'Test Project',
        'subdomain' => 'test-project',
        'description' => 'A test project',
        'settings' => ['collect_name' => true],
    ];

    $project = $repository->create($data);

    expect($project)->toBeInstanceOf(Project::class);
    expect($project->user_id)->toBe($user->id);
    expect($project->name)->toBe('Test Project');
    expect($project->subdomain)->toBe('test-project');
    expect($project->settings)->toBeArray();
    expect($project->settings)->toHaveKey('collect_name', true);

    // Verify the database was updated by querying it directly
    $dbProject = DB::table('projects')
        ->where('user_id', $user->id)
        ->where('name', 'Test Project')
        ->first();

    expect($dbProject)->not->toBeNull();
});

test('project repository can update a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create([
        'name' => 'Original Name',
        'subdomain' => 'original-subdomain',
        'description' => 'Original Description',
    ]);

    $repository = new ProjectRepository;

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $updatedProject = $repository->update($project, $data);

    expect($updatedProject->name)->toBe('Updated Name');
    expect($updatedProject->description)->toBe('Updated Description');
    expect($updatedProject->subdomain)->toBe('original-subdomain'); // Unchanged

    // Verify the database was updated by querying it directly
    $dbProject = DB::table('projects')
        ->where('id', $project->id)
        ->where('name', 'Updated Name')
        ->first();

    expect($dbProject)->not->toBeNull();
    // Decode JSON settings to verify they were updated
    $settings = json_decode($dbProject->settings, true);
    expect($settings)->toBeArray();
});

test('project repository can delete a project', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $project = Project::factory()->create();

    $repository = new ProjectRepository;
    $result = $repository->delete($project);

    expect($result)->toBeTrue();

    // Verify the database record was deleted by querying it directly
    $dbProject = DB::table('projects')
        ->where('id', $project->id)
        ->first();

    expect($dbProject)->toBeNull();
});

test('project repository can find projects by user', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create projects for user1
    Project::factory()->count(3)->create(['user_id' => $user1->id]);

    // Create projects for user2
    Project::factory()->count(2)->create(['user_id' => $user2->id]);

    $repository = new ProjectRepository;

    $user1Projects = $repository->findByUser($user1->id);
    $user2Projects = $repository->findByUser($user2->id);

    expect($user1Projects)->toHaveCount(3);
    expect($user2Projects)->toHaveCount(2);
    $user1Projects->each(function ($project) use ($user1) {
        expect($project->user_id)->toBe($user1->id);
    });
    $user2Projects->each(function ($project) use ($user2) {
        expect($project->user_id)->toBe($user2->id);
    });
});

test('project repository validates subdomain uniqueness', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $user = User::factory()->create();
    $repository = new ProjectRepository;

    // Create a project with a specific subdomain
    Project::factory()->create(['subdomain' => 'existing-subdomain', 'user_id' => $user->id]);

    // Try to create another project with the same subdomain
    $data = [
        'user_id' => $user->id,
        'name' => 'Test Project',
        'subdomain' => 'existing-subdomain', // Already exists
        'description' => 'A test project',
        'settings' => ['collect_name' => true],
    ];

    $result = $repository->create($data);

    expect($result)->toBeFalse();

    // Verify no new project was created with this subdomain
    $count = DB::table('projects')
        ->where('subdomain', 'existing-subdomain')
        ->count();

    expect($count)->toBe(1); // Only the original project should exist
});
