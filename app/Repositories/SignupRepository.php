<?php

namespace App\Repositories;

use App\Models\Signup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class SignupRepository
{
    public function create(array $data): Signup
    {
        return Signup::create($data);
    }

    public function update(Signup $signup, array $data): Signup
    {
        $signup->fill($data);
        $signup->save();

        // Reload the model to ensure changes are reflected
        $signup->refresh();

        return $signup;
    }

    public function delete(Signup $signup): bool
    {
        return (bool) $signup->delete();
    }

    public function sendVerificationEmail(Signup $signup): void
    {
        // In a real app, this would send an email with a verification link
        // Example implementation:
        /*
        Mail::to($signup->email)->send(new \App\Mail\VerifySignup($signup));
        */
    }

    public function verifyEmail(string $token): ?Signup
    {
        $signup = Signup::where('verification_token', $token)->first();

        if ($signup) {
            $signup->markAsVerified();
            $signup->save(); // Save changes to the database
        }

        return $signup;
    }

    public function getSignupsByProject(int $projectId, array $filters = []): Collection|LengthAwarePaginator
    {
        $query = Signup::where('project_id', $projectId);

        if (isset($filters['verified'])) {
            if ($filters['verified']) {
                $query->whereNotNull('verified_at');
            } else {
                $query->whereNull('verified_at');
            }
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('email', 'like', "%{$filters['search']}%")
                    ->orWhere('name', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return isset($filters['paginate']) ? $query->paginate($filters['paginate']) : $query->get();
    }

    public function getSignupsCountByDate(int $projectId, int $days = 30): array
    {
        return Signup::where('project_id', $projectId)
            ->whereDate('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
    }
}
