<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signup extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'email',
        'name',
        'ip_address',
        'user_agent',
        'referrer',
        'verification_token',
        'verified_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'verification_token',
        'ip_address',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function markAsVerified(): self
    {
        $this->verified_at = now();
        $this->verification_token = null;
        $this->save();

        return $this;
    }
}
