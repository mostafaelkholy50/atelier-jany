<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dynamically set the public storage URL to work correctly in subdirectories (e.g. XAMPP)
        if (!app()->runningInConsole()) {
            config(['filesystems.disks.public.url' => asset('storage')]);
        }
    }
}
