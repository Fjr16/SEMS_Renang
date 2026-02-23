<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticateController extends Controller
{
    public function index(){
        return view('pages.users.auth.index');
    }

    public function store(Request $req) : RedirectResponse
    {
        $credentials = $req->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$req->ip();

        if(RateLimiter::tooManyAttempts($throttleKey, 5)){
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam ' . $seconds . ' detik',
            ])->onlyInput('email');
        }

        $remember = $req->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);

            $req->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey,60);
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function destroy(Request $req){
        Auth::logout();

        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('dashboard');
    }
}
