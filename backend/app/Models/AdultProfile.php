<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdultProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'birth_date',
        'account_balance',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'account_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
