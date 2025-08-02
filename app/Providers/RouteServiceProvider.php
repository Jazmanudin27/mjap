<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        // ✅ Khusus subdomain sfamjap.aspartech.com
        Route::domain('sfamjap.aspartech.com')
            ->middleware('web')
            ->group(base_path('routes/mobile.php'));
    }
}
