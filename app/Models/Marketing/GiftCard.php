<?php

namespace App\Models\Marketing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'initial_balance',
        'balance',
        'purchaser_id',
        'recipient_id',
        'recipient_email',
        'message',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'balance' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function transactions()
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('balance', '>', 0)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    public static function generateCode()
    {
        do {
            $code = 'GC-' . strtoupper(substr(md5(uniqid()), 0, 12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function redeem($amount, $orderId = null)
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        $this->save();

        $this->transactions()->create([
            'order_id' => $orderId,
            'type' => 'redeem',
            'amount' => -$amount,
            'balance_after' => $this->balance,
        ]);

        if ($this->balance == 0) {
            $this->update(['status' => 'redeemed']);
        }

        return true;
    }
}
