<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // cek apakah login & role admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/'); // lempar user biasa ke beranda
        }

        return $next($request);
    }
}
