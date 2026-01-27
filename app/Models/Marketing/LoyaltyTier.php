<?php

namespace App\Models\Marketing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    protected $fillable = [
        'name',
        'description',
        'points_required',
        'discount_percentage',
        'benefits',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'discount_percentage' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'loyalty_tier_id');
    }

    /**
     * Get the next tier above this one
     */
    public function nextTier()
    {
        return static::where('points_required', '>', $this->points_required)
            ->where('is_active', true)
            ->orderBy('points_required')
            ->first();
    }
}
