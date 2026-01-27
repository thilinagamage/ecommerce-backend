<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product\Order;
use App\Services\LoyaltyService;

class BackfillLoyaltyPoints extends Command
{
    protected $signature = 'loyalty:backfill';
    protected $description = 'Award loyalty points for existing paid orders';

    public function handle(LoyaltyService $loyaltyService)
    {
        $orders = Order::where('payment_status', 'paid')
            ->whereNotNull('user_id')
            ->orderBy('created_at')
            ->get();

        $this->info("Found {$orders->count()} paid orders to process...");

        if ($orders->count() === 0) {
            $this->warn('No paid orders found!');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        $processed = 0;
        $skipped = 0;

        foreach ($orders as $order) {
            $transaction = $loyaltyService->awardPointsForOrder($order);

            if ($transaction) {
                $this->newLine();
                $this->info("✓ Order #{$order->order_number}: {$transaction->points} points awarded to {$order->user->name}");
                $processed++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Complete!");
        $this->info("Processed: {$processed}");
        $this->info("Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
