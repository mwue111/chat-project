<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['prefix' => 'api', 'middleware'=> ['auth:api']]); //todas las peticiones que tengan api como prefijo usarán el middleware auth.

        require base_path('routes/channels.php');
    }
}
