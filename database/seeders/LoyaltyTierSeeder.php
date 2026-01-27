<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marketing\LoyaltyTier;

class LoyaltyTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Bronze',
                'description' => 'Welcome tier for new members',
                'points_required' => 0,
                'discount_percentage' => 5,
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'description' => 'Exclusive benefits for regular shoppers',
                'points_required' => 500,
                'discount_percentage' => 10,
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'description' => 'Premium rewards for loyal customers',
                'points_required' => 1000,
                'discount_percentage' => 15,
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'description' => 'Elite status with maximum benefits',
                'points_required' => 2500,
                'discount_percentage' => 20,
                'priority' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            LoyaltyTier::create($tier);
        }
    }
}
