<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login()
    {
        if (Auth::check()) {
            if (request()->getHost() === 'sfamjap.aspartech.com') {
                return redirect()->route('viewDashboardSFAMobile');
            } else {
                return redirect()->route('dashboard');
            }
        }

        if (request()->getHost() === 'sfamjap.aspartech.com') {
            return view('mobile.auth.login');
        }

        return view('auth.login');
    }

    public function auth_login(Request $request)
    {
        $remember = $request->has('remember');
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan');
        }

        if ($user->status == 'Nonaktif') {
            return redirect()->back()->with('error', 'Akun Anda telah dinonaktifkan');
        }

        // Coba login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            logActivity('Login User', 'User ' . $request->name . ' login');

            if (request()->getHost() === 'sfamjap.aspartech.com') {
                return redirect()->route('mobile/viewDashboardSFAMobile');
            } else {
                return redirect()->route('login');
            }
        }

        return redirect()->back()->with('error', 'Periksa kembali Email & Password');
    }

    public function logout()
    {
        $user = User::where('id', Auth::id())->first();
        logActivity('Login User', 'User ' . $user->name . ' login');
        Auth::logout();
        return redirect(url(''));
    }
}
