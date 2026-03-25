<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentItem extends Model
{
    protected $fillable = [
        'payment_id',
        'type',
        'session_id',
        'month',
        'price',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function monthCoverage(): HasOne
    {
        return $this->hasOne(PaymentMonthCoverage::class);
    }
}
