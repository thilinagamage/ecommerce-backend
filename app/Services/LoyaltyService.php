<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product\Order;
use App\Models\Marketing\LoyaltyPointTransaction;
use App\Models\Marketing\LoyaltyTier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
     /**
     * Calculate points for an order
     */
    public function calculatePoints(Order $order): int
    {
        // 1 point per $1 spent (change this to your preference)
           // Use total instead of total_amount
        $amount = $order->total ?? $order->total_amount ?? 0;
        return (int) floor($amount);

        // Alternative: 1 point per $10 spent
        // return (int) floor($order->total_amount / 10);
    }

    /**
     * Award points for an order
     */
    public function awardPointsForOrder(Order $order): ?LoyaltyPointTransaction
    {
        Log::info('LoyaltyService: Attempting to award points', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount
        ]);

        if (!$order->user_id) {
            Log::warning('LoyaltyService: No user_id found', ['order_id' => $order->id]);
            return null;
        }

        if ($order->payment_status !== 'paid') {
            Log::info('LoyaltyService: Payment status not paid', [
                'order_id' => $order->id,
                'status' => $order->payment_status
            ]);
            return null;
        }

        // Check if points already awarded
        $existing = LoyaltyPointTransaction::where('order_id', $order->id)
            ->where('type', 'earned')
            ->first();

        if ($existing) {
            Log::info('LoyaltyService: Points already awarded', [
                'order_id' => $order->id,
                'transaction_id' => $existing->id
            ]);
            return $existing;
        }

        $points = $this->calculatePoints($order);

        if ($points <= 0) {
            Log::warning('LoyaltyService: No points to award', [
                'order_id' => $order->id,
                'calculated_points' => $points,
                'total_amount' => $order->total_amount
            ]);
            return null;
        }

        return DB::transaction(function () use ($order, $points) {
            $user = $order->user;

            Log::info('LoyaltyService: Creating transaction', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'points' => $points
            ]);

            // Update user points
            $user->increment('loyalty_points', $points);
            $user->increment('total_points_earned', $points);
            $user->refresh();

            // Create transaction
            $transaction = LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'earned',
                'points' => $points,
                'balance_after' => $user->loyalty_points,
                'description' => "Earned {$points} points from order #{$order->order_number}",
            ]);

            // Update tier if needed
            $user->updateLoyaltyTier();

            Log::info('LoyaltyService: Points awarded successfully', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'points' => $points,
                'new_balance' => $user->loyalty_points,
                'tier' => $user->loyaltyTier?->name ?? 'None',
            ]);

            return $transaction;
        });
    }

    /**
     * Redeem points
     */
    public function redeemPoints(User $user, int $points, string $description, ?Order $order = null): ?LoyaltyPointTransaction
    {
        if ($user->loyalty_points < $points) {
            throw new \Exception('Insufficient points balance');
        }

        return DB::transaction(function () use ($user, $points, $description, $order) {
            // Deduct points (negative value)
            $user->decrement('loyalty_points', $points);
            $user->refresh();

            return LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order?->id,
                'type' => 'redeemed',
                'points' => -$points,
                'balance_after' => $user->loyalty_points,
                'description' => $description,
            ]);
        });
    }

    /**
     * Adjust points manually (admin)
     */
    public function adjustPoints(User $user, int $points, string $reason): LoyaltyPointTransaction
    {
        return DB::transaction(function () use ($user, $points, $reason) {
            if ($points > 0) {
                $user->increment('loyalty_points', $points);
                $user->increment('total_points_earned', $points);
            } else {
                $user->decrement('loyalty_points', abs($points));
            }

            $user->refresh();

            $transaction = LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'type' => 'adjusted',
                'points' => $points,
                'balance_after' => $user->loyalty_points,
                'description' => $reason,
            ]);

            // Update tier
            $user->updateLoyaltyTier();

            return $transaction;
        });
    }

    /**
     * Get user's point summary
     */
    public function getUserSummary(User $user): array
    {
        return [
            'current_balance' => $user->loyalty_points,
            'total_earned' => $user->total_points_earned,
            'total_redeemed' => abs(LoyaltyPointTransaction::where('user_id', $user->id)
                ->where('type', 'redeemed')
                ->sum('points')),
            'current_tier' => $user->loyaltyTier,
            'next_tier' => $user->loyaltyTier?->nextTier(),
            'points_to_next_tier' => $this->getPointsToNextTier($user),
        ];
    }

    /**
     * Calculate points needed for next tier
     */
    protected function getPointsToNextTier(User $user): ?int
    {
        $nextTier = $user->loyaltyTier?->nextTier();

        if (!$nextTier) {
            $nextTier = LoyaltyTier::where('is_active', true)
                ->orderBy('points_required')
                ->first();
        }

        return $nextTier ? max(0, $nextTier->points_required - $user->total_points_earned) : null;
    }
}
