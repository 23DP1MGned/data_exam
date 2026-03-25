<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMonthCoverage extends Model
{
    protected $fillable = [
        'payment_id',
        'payment_item_id',
        'child_id',
        'group_id',
        'month',
        'covered_sessions_count',
        'amount',
    ];

    protected $casts = [
        'covered_sessions_count' => 'integer',
        'amount' => 'float',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
