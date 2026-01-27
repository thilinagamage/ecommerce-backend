<?php

namespace App\Models\Marketing;

use App\Models\Product\Order;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransaction extends Model
{
   protected $fillable = [
        'gift_card_id',
        'order_id',
        'type',
        'amount',
        'balance_after',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
