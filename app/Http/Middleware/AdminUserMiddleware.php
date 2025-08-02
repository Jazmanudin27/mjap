<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->status === 'Nonaktif') {
                Auth::logout();

                // Cek jika berasal dari route mobile
                if ($request->is('mobile/*')) {
                    return redirect()->route('loginMobile')->with('error', 'Akun Anda dinonaktifkan');
                }

                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan');
            }

            // Update last activity
            User::where('id', $user->id)->update(['last_activity' => now()]);
            return $next($request);
        }

        // Jika belum login
        if ($request->is('mobile/*')) {
            return redirect()->route('loginMobile')->with('error', 'Silakan login dulu');
        }

        return redirect()->route('login')->with('error', 'Silakan login dulu');
    }
}
