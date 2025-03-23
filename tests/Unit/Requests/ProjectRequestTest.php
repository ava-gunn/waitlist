<?php

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProjectRequestTest extends TestCase {}

test('project request validation passes with valid data', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    $user = User::factory()->create();
    $request = new ProjectRequest;
    $request->setUserResolver(fn () => $user);
    $request->replace([
        'name' => 'Test Project',
        'description' => 'A test project',
        'subdomain' => 'test-project',
        'settings' => [
            'collect_name' => true,
            'collect_email' => true,
        ],
    ]);

    expect($request->authorize())->toBeTrue();
    expect(validator($request->all(), $request->rules())->passes())->toBeTrue();
});

test('project request validation fails when name is missing', function () {
    $request = new ProjectRequest;
    $request->replace([
        'subdomain' => 'test-project',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('project request validation fails when subdomain has invalid format', function () {
    $request = new ProjectRequest;
    $request->replace([
        'name' => 'Test Project',
        'subdomain' => 'Invalid Subdomain',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('subdomain'))->toBeTrue();
});

test('project request validation fails when subdomain already exists', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    // Create a project with a subdomain
    Project::factory()->create(['subdomain' => 'existing-subdomain']);

    // Create request with same subdomain
    $request = new ProjectRequest;
    $request->replace([
        'name' => 'Another Project',
        'subdomain' => 'existing-subdomain',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('subdomain'))->toBeTrue();
});

test('project request validation allows existing subdomain for same project during update', function () {
    // Refresh the database
    Artisan::call('migrate:fresh');

    // Create a project
    $project = Project::factory()->create([
        'name' => 'Original Project Name',
        'subdomain' => 'my-subdomain',
    ]);

    // Create a request to update the project with the same subdomain
    $request = new ProjectRequest;
    $request->replace([
        'name' => 'Updated Project Name',
        'description' => 'Updated description',
        'subdomain' => 'my-subdomain', // Same as existing
    ]);

    // Create a mock route resolver for the update scenario
    $parameters = ['project' => $project];

    $request->setRouteResolver(function () use ($parameters) {
        $route = new Route('PUT', 'projects/{project}', []);
        $route->parameters = $parameters;

        return $route;
    });

    // Get the rules with the request object to ensure route parameters are recognized
    $rules = $request->rules();

    // Run validator against the rules
    $validator = validator($request->all(), $rules);

    // The validation should pass since it's the same project
    expect($validator->passes())->toBeTrue();
});

test('project request validates settings are an array', function () {
    $request = new ProjectRequest;
    $request->replace([
        'name' => 'Test Project',
        'subdomain' => 'test-project',
        'settings' => 'not-an-array',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('settings'))->toBeTrue();
});

test('project request validates boolean settings values', function () {
    $request = new ProjectRequest;
    $request->replace([
        'name' => 'Test Project',
        'subdomain' => 'test-project',
        'settings' => [
            'collect_name' => 'not-a-boolean',
            'collect_email' => true,
        ],
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('settings.collect_name'))->toBeTrue();
});
