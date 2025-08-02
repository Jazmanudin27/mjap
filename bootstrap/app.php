<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminUserMiddleware;
use App\Http\Middleware\UpdateLastActivity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

        then: function () {
            \Illuminate\Support\Facades\Route::prefix('mobile')
                ->middleware('web')
                ->group(base_path('routes/mobile.php'));

            \Illuminate\Support\Facades\Route::prefix('presensi')
                ->middleware('web')
                ->group(base_path('routes/presensi.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'useradmin' => AdminUserMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
