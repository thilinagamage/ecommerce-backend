<?php

namespace App\Models\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
   protected $fillable = [
        'order_id',
        'user_id',
        'refund_number',
        'amount',
        'status',
        'reason',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateRefundNumber()
    {
        do {
            $refundNumber = 'REF-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('refund_number', $refundNumber)->exists());

        return $refundNumber;
    }
}
