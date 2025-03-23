<?php

namespace Database\Factories;

use App\Models\WaitlistTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WaitlistTemplateFactory extends Factory
{
    protected $model = WaitlistTemplate::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'structure' => [
                'components' => [
                    [
                        'type' => 'header',
                        'content' => 'Join Our Waitlist',
                        'level' => 1,
                    ],
                    [
                        'type' => 'text',
                        'content' => 'Be the first to know when we launch!',
                    ],
                    [
                        'type' => 'form',
                        'fields' => [
                            [
                                'name' => 'email',
                                'type' => 'email',
                                'label' => 'Email',
                                'placeholder' => 'Enter your email',
                                'required' => true,
                            ],
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'label' => 'Name',
                                'placeholder' => 'Enter your name',
                                'required' => false,
                            ],
                        ],
                        'button' => [
                            'text' => 'Join Waitlist',
                            'color' => 'primary',
                        ],
                    ],
                ],
                'settings' => [
                    'backgroundColor' => '#ffffff',
                    'textColor' => '#333333',
                    'buttonColor' => '#4f46e5',
                    'buttonTextColor' => '#ffffff',
                ],
            ],
            'is_active' => true,
            'is_default' => false,
        ];
    }

    public function default(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
