<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'group_id',
        'session_template_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'price',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'float',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function sessionTemplate(): BelongsTo
    {
        return $this->belongsTo(SessionTemplate::class, 'session_template_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(PaymentItem::class, 'session_id');
    }

    public function extraChildren(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_children', 'session_id', 'user_id')->withTimestamps();
    }

    public function effectiveChildren()
    {
        $this->loadMissing(['group.children', 'extraChildren']);

        return $this->group->children
            ->concat($this->extraChildren)
            ->unique('id')
            ->sortBy(fn (User $child) => strtolower(trim($child->name.' '.$child->surname)))
            ->values();
    }
}
