<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WaitlistTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'structure',
        'is_active',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('customizations', 'is_active')
            ->withTimestamps()
            ->using(ProjectWaitlistTemplatePivot::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
