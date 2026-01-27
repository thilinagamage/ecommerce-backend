<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Marketing\LoyaltyTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'loyalty_points',
        'total_points_earned',
        'loyalty_tier_id',
    ];

    protected $dates = [
        'deleted_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'loyalty_points' => 'integer',
            'total_points_earned' => 'integer',
            'loyalty_tier_id' => 'integer',         
        ];
    }

    public function loyaltyTransactions()
{
    return $this->hasMany(\App\Models\Marketing\LoyaltyPointTransaction::class);
}

public function loyaltyTier()
{
    return $this->belongsTo(\App\Models\Marketing\LoyaltyTier::class);
}

public function orders()
{
    return $this->hasMany(\App\Models\Product\Order::class);
}

/**
 * Update user's loyalty tier based on total points earned
 */
/**
 * Update user's loyalty tier based on total points earned
 */
public function updateLoyaltyTier()
{
    $tier = \App\Models\Marketing\LoyaltyTier::where('is_active', true)
        ->where('points_required', '<=', $this->total_points_earned)
        ->orderBy('points_required', 'desc')
        ->first();

    if ($tier && $this->loyalty_tier_id !== $tier->id) {
        $this->update(['loyalty_tier_id' => $tier->id]);
        return true;
    }

    return false;
}

}
