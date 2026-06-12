<?php

namespace App\Providers;

use App\Models\Guru;
use App\Observers\GuruObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Observer untuk memastikan konsistensi email guru dan user
        Guru::observe(GuruObserver::class);

        Paginator::useTailwind();
    }
}
