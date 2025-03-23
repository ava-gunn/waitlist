<?php

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;

test('user can view their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $policy = new ProjectPolicy;
    $result = $policy->view($user, $project);

    expect($result)->toBeTrue();
});

test('user cannot view another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $policy = new ProjectPolicy;
    $result = $policy->view($user, $project);

    expect($result)->toBeFalse();
});

test('user can update their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $policy = new ProjectPolicy;
    $result = $policy->update($user, $project);

    expect($result)->toBeTrue();
});

test('user cannot update another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $policy = new ProjectPolicy;
    $result = $policy->update($user, $project);

    expect($result)->toBeFalse();
});

test('user can delete their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $policy = new ProjectPolicy;
    $result = $policy->delete($user, $project);

    expect($result)->toBeTrue();
});

test('user cannot delete another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $policy = new ProjectPolicy;
    $result = $policy->delete($user, $project);

    expect($result)->toBeFalse();
});

test('user can view signups for their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $policy = new ProjectPolicy;
    $result = $policy->viewSignups($user, $project);

    expect($result)->toBeTrue();
});

test('user cannot view signups for another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $policy = new ProjectPolicy;
    $result = $policy->viewSignups($user, $project);

    expect($result)->toBeFalse();
});
