<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
{
    $request->validate([
        'email_or_phone' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('email_or_phone', $request->email_or_phone)->first();

    if (!$user) {
        return back()->withErrors([
            'email_or_phone' => 'Email/nomor telepon tidak ditemukan.',
        ])->onlyInput('email_or_phone');
    }

    $remember = $request->filled('remember');

    if (!Auth::attempt(
        ['email_or_phone' => $request->email_or_phone, 'password' => $request->password],
        $remember 
    )) {
        return back()->withErrors([
            'password' => 'Password yang Anda masukkan salah.', 
        ])->withInput($request->only('email_or_phone'));
    }

    $request->session()->regenerate();

    return redirect()->intended(
        Auth::user()->role === 'admin' ? '/admin/dashboard' : '/'
    );
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}