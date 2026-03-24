<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionTemplate extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'weekdays',
        'excluded_dates',
        'starts_on',
        'start_time',
        'end_time',
        'price',
        'is_active',
    ];

    protected $casts = [
        'weekdays' => 'array',
        'excluded_dates' => 'array',
        'starts_on' => 'date',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'session_template_id');
    }
}
