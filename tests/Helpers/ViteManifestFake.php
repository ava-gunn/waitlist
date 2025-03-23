<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\File;

class ViteManifestFake
{
    public static function create(): void
    {
        // Create a directory for the manifest if it doesn't exist
        $manifestDir = public_path('build/assets');
        if (! File::exists($manifestDir)) {
            File::makeDirectory($manifestDir, 0755, true);
        }

        // Create a fake manifest.json file
        $manifest = [
            'resources/js/app.tsx' => [
                'file' => 'assets/app-hash.js',
                'src' => 'resources/js/app.tsx',
                'isEntry' => true,
            ],
            'resources/js/pages/dashboard.tsx' => [
                'file' => 'assets/dashboard-hash.js',
                'src' => 'resources/js/pages/dashboard.tsx',
            ],
            'resources/js/pages/welcome.tsx' => [
                'file' => 'assets/welcome-hash.js',
                'src' => 'resources/js/pages/welcome.tsx',
            ],
            'resources/js/pages/Projects/Index.tsx' => [
                'file' => 'assets/Projects/Index-hash.js',
                'src' => 'resources/js/pages/Projects/Index.tsx',
            ],
            'resources/js/pages/Projects/Create.tsx' => [
                'file' => 'assets/Projects/Create-hash.js',
                'src' => 'resources/js/pages/Projects/Create.tsx',
            ],
            'resources/js/pages/Projects/Show.tsx' => [
                'file' => 'assets/Projects/Show-hash.js',
                'src' => 'resources/js/pages/Projects/Show.tsx',
            ],
            'resources/js/pages/Projects/Edit.tsx' => [
                'file' => 'assets/Projects/Edit-hash.js',
                'src' => 'resources/js/pages/Projects/Edit.tsx',
            ],
            'resources/js/pages/Projects/Templates/Index.tsx' => [
                'file' => 'assets/Projects/Templates/Index-hash.js',
                'src' => 'resources/js/pages/Projects/Templates/Index.tsx',
            ],
            'resources/js/pages/Projects/Templates/Edit.tsx' => [
                'file' => 'assets/Projects/Templates/Edit-hash.js',
                'src' => 'resources/js/pages/Projects/Templates/Edit.tsx',
            ],
            'resources/js/pages/Projects/Signups/Index.tsx' => [
                'file' => 'assets/Projects/Signups/Index-hash.js',
                'src' => 'resources/js/pages/Projects/Signups/Index.tsx',
            ],
            'resources/js/pages/Public/Signup.tsx' => [
                'file' => 'assets/Public/Signup-hash.js',
                'src' => 'resources/js/pages/Public/Signup.tsx',
            ],
            // Auth pages
            'resources/js/pages/auth/login.tsx' => [
                'file' => 'assets/auth/login-hash.js',
                'src' => 'resources/js/pages/auth/login.tsx',
            ],
            'resources/js/pages/auth/register.tsx' => [
                'file' => 'assets/auth/register-hash.js',
                'src' => 'resources/js/pages/auth/register.tsx',
            ],
            'resources/js/pages/auth/forgot-password.tsx' => [
                'file' => 'assets/auth/forgot-password-hash.js',
                'src' => 'resources/js/pages/auth/forgot-password.tsx',
            ],
            'resources/js/pages/auth/reset-password.tsx' => [
                'file' => 'assets/auth/reset-password-hash.js',
                'src' => 'resources/js/pages/auth/reset-password.tsx',
            ],
            'resources/js/pages/auth/confirm-password.tsx' => [
                'file' => 'assets/auth/confirm-password-hash.js',
                'src' => 'resources/js/pages/auth/confirm-password.tsx',
            ],
            'resources/js/pages/auth/verify-email.tsx' => [
                'file' => 'assets/auth/verify-email-hash.js',
                'src' => 'resources/js/pages/auth/verify-email.tsx',
            ],
            // Profile pages
            'resources/js/pages/profile/edit.tsx' => [
                'file' => 'assets/profile/edit-hash.js',
                'src' => 'resources/js/pages/profile/edit.tsx',
            ],
            'resources/js/pages/profile/partials/update-password-form.tsx' => [
                'file' => 'assets/profile/partials/update-password-form-hash.js',
                'src' => 'resources/js/pages/profile/partials/update-password-form.tsx',
            ],
            'resources/js/pages/profile/partials/update-profile-information-form.tsx' => [
                'file' => 'assets/profile/partials/update-profile-information-form-hash.js',
                'src' => 'resources/js/pages/profile/partials/update-profile-information-form.tsx',
            ],
            'resources/js/pages/profile/partials/delete-user-form.tsx' => [
                'file' => 'assets/profile/partials/delete-user-form-hash.js',
                'src' => 'resources/js/pages/profile/partials/delete-user-form.tsx',
            ],
            // Settings pages
            'resources/js/pages/settings/profile.tsx' => [
                'file' => 'assets/settings/profile-hash.js',
                'src' => 'resources/js/pages/settings/profile.tsx',
            ],
            'resources/js/pages/settings/password.tsx' => [
                'file' => 'assets/settings/password-hash.js',
                'src' => 'resources/js/pages/settings/password.tsx',
            ],
            // UI Components
            'resources/js/components/ui/pagination.tsx' => [
                'file' => 'assets/components/ui/pagination-hash.js',
                'src' => 'resources/js/components/ui/pagination.tsx',
            ],
            'resources/js/components/ui/badge.tsx' => [
                'file' => 'assets/components/ui/badge-hash.js',
                'src' => 'resources/js/components/ui/badge.tsx',
            ],
            'resources/js/components/ui/dialog.tsx' => [
                'file' => 'assets/components/ui/dialog-hash.js',
                'src' => 'resources/js/components/ui/dialog.tsx',
            ],
            'resources/js/components/ui/button.tsx' => [
                'file' => 'assets/components/ui/button-hash.js',
                'src' => 'resources/js/components/ui/button.tsx',
            ],
            'resources/js/components/ui/form.tsx' => [
                'file' => 'assets/components/ui/form-hash.js',
                'src' => 'resources/js/components/ui/form.tsx',
            ],
            'resources/js/components/ui/input.tsx' => [
                'file' => 'assets/components/ui/input-hash.js',
                'src' => 'resources/js/components/ui/input.tsx',
            ],
            'resources/js/components/ui/toast.tsx' => [
                'file' => 'assets/components/ui/toast-hash.js',
                'src' => 'resources/js/components/ui/toast.tsx',
            ],
            'resources/js/components/ui/toaster.tsx' => [
                'file' => 'assets/components/ui/toaster-hash.js',
                'src' => 'resources/js/components/ui/toaster.tsx',
            ],
            'resources/js/components/ui/use-toast.ts' => [
                'file' => 'assets/components/ui/use-toast-hash.js',
                'src' => 'resources/js/components/ui/use-toast.ts',
            ],
            // Add CSS files
            'resources/css/app.css' => [
                'file' => 'assets/app-hash.css',
                'src' => 'resources/css/app.css',
            ],
        ];

        File::put(public_path('build/manifest.json'), json_encode($manifest, JSON_PRETTY_PRINT));
    }

    public static function delete(): void
    {
        if (File::exists(public_path('build/manifest.json'))) {
            File::delete(public_path('build/manifest.json'));
        }
    }
}
