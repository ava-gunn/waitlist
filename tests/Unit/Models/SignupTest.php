<?php

use App\Models\Project;
use App\Models\Signup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

test('signup has correct fillable attributes', function () {
    $fillable = [
        'project_id',
        'email',
        'name',
        'ip_address',
        'user_agent',
        'referrer',
        'verification_token',
        'verified_at',
    ];
    expect((new Signup)->getFillable())->toBe($fillable);
});

test('signup belongs to a project', function () {
    $project = Project::factory()->create();
    $signup = Signup::factory()->create(['project_id' => $project->id]);

    expect($signup->project)->toBeInstanceOf(Project::class);
    expect($signup->project->id)->toBe($project->id);
});

test('signup verified at is cast to datetime', function () {
    $now = now();
    $signup = Signup::factory()->create(['verified_at' => $now]);

    expect($signup->verified_at)->toBeInstanceOf(Carbon::class);
    expect($signup->verified_at->timestamp)->toBe($now->timestamp);
});

test('signup can be marked as verified', function () {
    $signup = Signup::factory()->create(['verified_at' => null]);

    expect($signup->verified_at)->toBeNull();

    $signup->markAsVerified();

    expect($signup->verified_at)->toBeInstanceOf(Carbon::class);
    expect($signup->verified_at->timestamp)->toBeGreaterThanOrEqual(now()->subMinute()->timestamp);
    expect($signup->verified_at->timestamp)->toBeLessThanOrEqual(now()->timestamp);
});

test('signup verified scope returns only verified signups', function () {
    // Clear the database first
    Artisan::call('migrate:fresh');

    // Create unverified signups
    Signup::factory()->count(2)->create(['verified_at' => null]);

    // Create verified signups
    Signup::factory()->count(3)->create(['verified_at' => now()]);

    $verifiedSignups = Signup::verified()->get();

    expect($verifiedSignups)->toHaveCount(3);
    $verifiedSignups->each(function ($signup) {
        expect($signup->verified_at)->not->toBeNull();
    });
});

test('signup unverified scope returns only unverified signups', function () {
    // Clear the database first
    Artisan::call('migrate:fresh');

    // Create unverified signups
    Signup::factory()->count(2)->create(['verified_at' => null]);

    // Create verified signups
    Signup::factory()->count(3)->create(['verified_at' => now()]);

    $unverifiedSignups = Signup::unverified()->get();

    expect($unverifiedSignups)->toHaveCount(2);
    $unverifiedSignups->each(function ($signup) {
        expect($signup->verified_at)->toBeNull();
    });
});

test('signup is verified helper returns correct boolean value', function () {
    $verifiedSignup = Signup::factory()->create(['verified_at' => now()]);
    $unverifiedSignup = Signup::factory()->create(['verified_at' => null]);

    expect($verifiedSignup->isVerified())->toBeTrue();
    expect($unverifiedSignup->isVerified())->toBeFalse();
});
