<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_spend',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'starts_at',
        'expires_at',
        'product_ids',
        'category_ids',
        'excluded_product_ids',
        'excluded_category_ids',
        'first_order_only',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'product_ids' => 'array',
        'category_ids' => 'array',
        'excluded_product_ids' => 'array',
        'excluded_category_ids' => 'array',
        'first_order_only' => 'boolean',
    ];

    // Relationships
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    // Methods
    public function isValid($userId = null, $cartTotal = 0)
    {
        // Check status
        if ($this->status !== 'active') {
            return ['valid' => false, 'message' => 'Coupon is not active'];
        }

        // Check dates
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return ['valid' => false, 'message' => 'Coupon is not yet valid'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Coupon has expired'];
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached'];
        }

        // Check per-user limit
        if ($userId && $this->usage_limit_per_user) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon'];
            }
        }

        // Check minimum spend
        if ($this->minimum_spend && $cartTotal < $this->minimum_spend) {
            return ['valid' => false, 'message' => 'Minimum spend of $' . number_format($this->minimum_spend, 2) . ' required'];
        }

        return ['valid' => true, 'message' => 'Coupon is valid'];
    }

    public function calculateDiscount($subtotal)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;

            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }

            return $discount;
        }

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }

        return 0; // free_shipping handled separately
    }

    public function incrementUsage($userId = null, $orderId = null, $discountAmount = 0)
    {
        $this->increment('usage_count');

        if ($userId || $orderId) {
            $this->usages()->create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'discount_amount' => $discountAmount,
            ]);
        }
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'scheduled' => 'info',
            'expired' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
