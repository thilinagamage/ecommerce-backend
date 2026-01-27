<?php

namespace App\Providers;

use App\Models\Product\Order;
use App\Observers\OrderObserver;
use App\Services\LoyaltyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoyaltyService::class);
    }

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
    }
}
