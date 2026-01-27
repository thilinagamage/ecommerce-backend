<?php

namespace App\Models\Marketing;

use App\Models\Product\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'points',
        'balance_after',
        'description',
        'expires_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
