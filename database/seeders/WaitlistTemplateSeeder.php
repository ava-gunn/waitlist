<?php

namespace Database\Seeders;

use App\Models\WaitlistTemplate;
use Illuminate\Database\Seeder;

class WaitlistTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Clean existing templates if needed
        // WaitlistTemplate::truncate();

        // Create side-by-side template (split layout)
        WaitlistTemplate::create([
            'name' => 'Side-by-Side Modern',
            'slug' => 'side-by-side-modern',
            'description' => 'A modern, side-by-side layout with image and form, perfect for product launches.',
            'structure' => [
                'template_type' => 'side-by-side',
                'settings' => [
                    'backgroundColor' => '#ffffff',
                    'textColor' => '#1f2937',
                    'buttonColor' => '#4f46e5',
                    'buttonTextColor' => '#ffffff',
                    'accentColor' => '#8b5cf6',
                ],
                'components' => [
                    [
                        'type' => 'layout',
                        'variant' => 'side-by-side',
                        'children' => [
                            [
                                'type' => 'image',
                                'position' => 'left',
                                'src' => '/images/templates/side-by-side-illustration.svg',
                                'alt' => 'Product illustration',
                            ],
                            [
                                'type' => 'content',
                                'position' => 'right',
                                'children' => [
                                    [
                                        'type' => 'header',
                                        'level' => 1,
                                        'content' => 'Join our exclusive waitlist',
                                        'className' => 'text-3xl font-bold tracking-tight sm:text-4xl',
                                    ],
                                    [
                                        'type' => 'text',
                                        'content' => 'Be among the first to experience our new product. Sign up now and receive early access when we launch.',
                                        'className' => 'mt-4 text-lg',
                                    ],
                                    [
                                        'type' => 'form',
                                        'className' => 'mt-8 flex flex-col space-y-4',
                                        'fields' => [
                                            [
                                                'type' => 'text',
                                                'name' => 'name',
                                                'placeholder' => 'Your name',
                                                'label' => 'Full name',
                                                'required' => true,
                                            ],
                                            [
                                                'type' => 'email',
                                                'name' => 'email',
                                                'placeholder' => 'your.email@example.com',
                                                'label' => 'Email address',
                                                'required' => true,
                                            ],
                                        ],
                                        'button' => [
                                            'text' => 'Join the waitlist',
                                            'className' => 'mt-4 w-full',
                                        ],
                                        'success_message' => "Thanks for joining! We'll keep you updated.",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'thumbnail_path' => '/images/templates/side-by-side-thumbnail.jpg',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Create one-page centered template
        WaitlistTemplate::create([
            'name' => 'One-Page Centered',
            'slug' => 'one-page-centered',
            'description' => 'A clean, centered layout with a prominent call-to-action, ideal for simple waitlists.',
            'structure' => [
                'template_type' => 'single-column',
                'settings' => [
                    'backgroundColor' => '#f9fafb',
                    'textColor' => '#111827',
                    'buttonColor' => '#4f46e5',
                    'buttonTextColor' => '#ffffff',
                    'accentColor' => '#8b5cf6',
                ],
                'components' => [
                    [
                        'type' => 'layout',
                        'variant' => 'centered',
                        'className' => 'max-w-3xl mx-auto px-4 py-16 sm:py-24 sm:px-6 lg:px-8',
                        'children' => [
                            [
                                'type' => 'header',
                                'level' => 1,
                                'content' => 'Get early access',
                                'className' => 'text-4xl font-extrabold tracking-tight text-center sm:text-5xl',
                            ],
                            [
                                'type' => 'text',
                                'content' => 'Our product is launching soon. Join the waitlist to be notified when we go live and receive exclusive early access.',
                                'className' => 'mt-4 text-xl text-center text-gray-500',
                            ],
                            [
                                'type' => 'form',
                                'className' => 'mt-12 sm:mx-auto sm:max-w-lg',
                                'fields' => [
                                    [
                                        'type' => 'text',
                                        'name' => 'name',
                                        'placeholder' => 'Your name',
                                        'label' => 'Full name',
                                        'required' => true,
                                    ],
                                    [
                                        'type' => 'email',
                                        'name' => 'email',
                                        'placeholder' => 'your.email@example.com',
                                        'label' => 'Email address',
                                        'required' => true,
                                    ],
                                ],
                                'button' => [
                                    'text' => 'Join now',
                                    'className' => 'mt-4 w-full',
                                ],
                                'success_message' => "You're on the list! We'll be in touch soon.",
                            ],
                        ],
                    ],
                ],
            ],
            'thumbnail_path' => '/images/templates/one-page-thumbnail.jpg',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Create split screen dark template
        WaitlistTemplate::create([
            'name' => 'Split Screen Dark',
            'slug' => 'split-screen-dark',
            'description' => 'A bold, dark-themed split screen layout with vibrant accent colors.',
            'structure' => [
                'template_type' => 'split-screen',
                'settings' => [
                    'backgroundColor' => '#111827',
                    'textColor' => '#f9fafb',
                    'buttonColor' => '#8b5cf6',
                    'buttonTextColor' => '#ffffff',
                    'accentColor' => '#ec4899',
                ],
                'components' => [
                    [
                        'type' => 'layout',
                        'variant' => 'split-screen',
                        'children' => [
                            [
                                'type' => 'content',
                                'position' => 'left',
                                'className' => 'bg-gradient-to-br from-purple-800 to-indigo-900 flex items-center justify-center',
                                'children' => [
                                    [
                                        'type' => 'div',
                                        'className' => 'max-w-md px-8 py-12',
                                        'children' => [
                                            [
                                                'type' => 'header',
                                                'level' => 1,
                                                'content' => 'Launching Soon',
                                                'className' => 'text-4xl font-extrabold tracking-tight text-white sm:text-5xl',
                                            ],
                                            [
                                                'type' => 'text',
                                                'content' => 'Our revolutionary platform is almost ready. Be the first to know when we launch.',
                                                'className' => 'mt-4 text-xl text-purple-100',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'content',
                                'position' => 'right',
                                'className' => 'flex items-center justify-center',
                                'children' => [
                                    [
                                        'type' => 'div',
                                        'className' => 'max-w-md w-full px-8 py-12',
                                        'children' => [
                                            [
                                                'type' => 'header',
                                                'level' => 2,
                                                'content' => 'Join the waitlist',
                                                'className' => 'text-2xl font-bold',
                                            ],
                                            [
                                                'type' => 'form',
                                                'className' => 'mt-8 space-y-6',
                                                'fields' => [
                                                    [
                                                        'type' => 'text',
                                                        'name' => 'name',
                                                        'placeholder' => 'Your name',
                                                        'label' => 'Full name',
                                                        'required' => true,
                                                    ],
                                                    [
                                                        'type' => 'email',
                                                        'name' => 'email',
                                                        'placeholder' => 'your.email@example.com',
                                                        'label' => 'Email address',
                                                        'required' => true,
                                                    ],
                                                ],
                                                'button' => [
                                                    'text' => 'Reserve my spot',
                                                    'className' => 'mt-4 w-full',
                                                ],
                                                'success_message' => "You're in! We'll notify you when we launch.",
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'thumbnail_path' => '/images/templates/split-screen-dark-thumbnail.jpg',
            'is_active' => true,
            'is_default' => false,
        ]);
    }
}
