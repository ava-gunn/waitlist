<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subdomain',
        'settings',
        'logo_path',
        'is_active',
        'user_id',
        'waitlist_template_id',
        'template_customizations',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'template_customizations' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function waitlistTemplate(): BelongsTo
    {
        return $this->belongsTo(WaitlistTemplate::class);
    }

    public function signups(): HasMany
    {
        return $this->hasMany(Signup::class);
    }

    public function hasSignups(): bool
    {
        return $this->signups()->count() > 0;
    }

    public function getFullUrlAttribute(): string
    {
        return 'http://' . $this->subdomain . '.' . config('app.domain');
    }
}
