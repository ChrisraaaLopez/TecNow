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
        // Auto-crear el symlink de storage si no existe (necesario en Laravel Cloud)
        $link   = public_path('storage');
        $target = storage_path('app/public');
        if (!file_exists($link) && is_dir($target)) {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
        }
    }
}
