<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'group_number',
        'age_category',
        'schedule_days',
        'default_time',
        'price',
        'coach_id',
    ];

    protected $appends = [
        'group_code',
        'display_name',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'group_id');
    }

    public function sessionTemplates(): HasMany
    {
        return $this->hasMany(SessionTemplate::class, 'group_id');
    }

    public function getGroupCodeAttribute(): string
    {
        $number = (int) ($this->group_number ?? $this->id ?? 0);

        return 'G-'.str_pad((string) max($number, 1), 3, '0', STR_PAD_LEFT);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->name.' ('.$this->group_code.')');
    }
}
