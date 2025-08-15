<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            // Kalau user biasa, arahkan ke beranda
            if (Auth::user()->role === 'user') {
                return redirect('/');
            }

            // Kalau admin, arahkan ke dashboard admin
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }

            // Default
            return redirect('/');
        }

        return $next($request);
    }
}
