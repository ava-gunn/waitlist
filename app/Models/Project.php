<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function waitlistTemplates(): BelongsToMany
    {
        return $this->belongsToMany(WaitlistTemplate::class)
            ->withPivot('customizations', 'is_active')
            ->withTimestamps()
            ->using(ProjectWaitlistTemplatePivot::class);
    }

    public function signups(): HasMany
    {
        return $this->hasMany(Signup::class);
    }

    public function getActiveTemplateAttribute(): ?WaitlistTemplate
    {
        return $this->waitlistTemplates()
            ->wherePivot('is_active', true)
            ->first();
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
