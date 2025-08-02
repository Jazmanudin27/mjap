<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->is('mobile/*')) {
                return route('loginMobile');
            }

            return route('login');
        }

        return null;
    }
}
