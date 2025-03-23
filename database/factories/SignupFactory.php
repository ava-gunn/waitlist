<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Signup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SignupFactory extends Factory
{
    protected $model = Signup::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'metadata' => [
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'device' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
                'source' => $this->faker->randomElement(['organic', 'social', 'referral']),
            ],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'referrer' => $this->faker->url(),
            'verification_token' => Str::random(64),
            'verified_at' => null,
        ];
    }

    public function verified(): self
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function forProject(Project $project): self
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }
}
