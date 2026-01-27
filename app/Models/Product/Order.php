<?php

namespace App\Models\Product;

use App\Models\Marketing\LoyaltyPointTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'same_as_billing',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total',
        'coupon_code',
        'customer_notes',
        'admin_notes',
        'shipping_method',
        'tracking_number',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'same_as_billing' => 'boolean',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class);
    }

    public function refunds()
    {
        return $this->hasMany(OrderRefund::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'failed' => 'dark',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            'partially_refunded' => 'info',
        ];

        return $badges[$this->payment_status] ?? 'secondary';
    }

    public function getFullBillingAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->billing_address_line1,
            $this->billing_address_line2,
            $this->billing_city,
            $this->billing_state,
            $this->billing_postal_code,
            $this->billing_country,
        ]));
    }

    public function getFullShippingAddressAttribute()
    {
        if ($this->same_as_billing) {
            return $this->full_billing_address;
        }

        return implode(', ', array_filter([
            $this->shipping_address_line1,
            $this->shipping_address_line2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country,
        ]));
    }

    // Methods
    public function updateStatus($newStatus, $note = null, $notifyCustomer = false)
    {
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'customer_notified' => $notifyCustomer,
            'user_id' => auth()->id(),
        ]);

        // Update timestamps based on status
        if ($newStatus === 'shipped' && !$this->shipped_at) {
            $this->update(['shipped_at' => now()]);
        }

        if ($newStatus === 'delivered' && !$this->delivered_at) {
            $this->update(['delivered_at' => now()]);
        }
    }

    public function addNote($note, $customerVisible = false)
    {
        return $this->notes()->create([
            'note' => $note,
            'customer_visible' => $customerVisible,
            'user_id' => auth()->id(),
        ]);
    }

    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }


    public function canBeRefunded()
    {
        return $this->payment_status === 'paid' &&
            in_array($this->status, ['delivered', 'shipped', 'processing']);
    }

    public function getTotalRefundedAttribute()
    {
        return $this->refunds()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');
    }

    public function getRemainingRefundableAmountAttribute()
    {
        return $this->total - $this->total_refunded;
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyPointTransaction::class);
    }
}
