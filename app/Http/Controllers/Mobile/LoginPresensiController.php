<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginPresensiController extends Controller
{
    public function loginPresensi()
    {
        return view('mobile.auth.loginPresensi');
    }
    public function authPresensi(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('viewDashboardPresensi');
        }

        return back()->withErrors([
            'email' => 'nik atau password salah.',
        ]);
    }

    public function logoutPresensi(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loginPresensi');
    }
}
