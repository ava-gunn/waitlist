<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the test user or create one if needed
        $user = User::firstWhere('email', 'test@example.com') ??
                User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);

        // Create test projects for the user
        for ($i = 1; $i <= 3; $i++) {
            Project::create([
                'name' => "Test Project {$i}",
                'subdomain' => "project{$i}",
                'description' => "This is a test project {$i} for demonstration purposes.",
                'settings' => [
                    'theme' => 'light',
                    'collect_name' => true,
                    'social_sharing' => true,
                ],
                'is_active' => true,
                'user_id' => $user->id,
            ]);
        }
    }
}
