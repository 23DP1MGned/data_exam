<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionCancellationCredit extends Model
{
    protected $fillable = [
        'session_id',
        'parent_id',
        'child_id',
        'payment_id',
        'payment_item_id',
        'payment_month_coverage_id',
        'source_type',
        'amount',
        'credited_at',
        'reversed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'credited_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function paymentMonthCoverage(): BelongsTo
    {
        return $this->belongsTo(PaymentMonthCoverage::class);
    }
}
