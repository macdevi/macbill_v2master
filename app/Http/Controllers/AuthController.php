<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectByRole($user->role ?? null);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.'
        ])->onlyInput('username');
    }

    private function redirectByRole(?string $role)
    {
        return match ($role) {
            'super_admin' => redirect('/dashboard'),
            'admin'       => redirect('/admin/dashboard'),
            default           => redirect('/staff-home'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
