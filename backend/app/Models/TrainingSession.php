<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'group_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(PaymentItem::class, 'session_id');
    }
}
