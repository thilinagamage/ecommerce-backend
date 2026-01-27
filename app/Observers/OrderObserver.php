<?php

namespace App\Observers;

use App\Models\Marketing\LoyaltyPointTransaction;
use App\Models\Product\Order;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
     protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        Log::info('OrderObserver: Order created', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount
        ]);

        $this->loyaltyService->awardPointsForOrder($order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        Log::info('OrderObserver: Order updated', [
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'is_dirty' => $order->isDirty('payment_status'),
            'original_status' => $order->getOriginal('payment_status'),
            'new_status' => $order->payment_status
        ]);

        // Award points when payment status changes to paid
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            Log::info('OrderObserver: Payment status changed to paid, awarding points');
            $this->loyaltyService->awardPointsForOrder($order);
        }
    }
}
