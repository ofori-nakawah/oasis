<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Validate P2P payment percentages sum to 100
        $initialPercentage = config('p2p.initial_payment_percentage', 10);
        $finalPercentage = config('p2p.final_payment_percentage', 90);
        
        if (($initialPercentage + $finalPercentage) !== 100) {
            throw new \RuntimeException(
                "P2P payment percentages must sum to 100. Current values: initial={$initialPercentage}%, final={$finalPercentage}%"
            );
        }
    }
}
