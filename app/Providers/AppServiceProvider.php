<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

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
            // Listen to user registration event and set default role
            Event::listen(Registered::class, function ($event) {
                if (!$event->user->role) {
                    $event->user->role = 'hrga';
                    $event->user->save();
                }
            });
        }
}