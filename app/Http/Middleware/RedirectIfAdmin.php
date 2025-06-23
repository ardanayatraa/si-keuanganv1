<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{
    /**
     * Jika admin sudah login dan mencoba ke area pengguna,
     * redirect ke admin dashboard.
     */
    public function handle(Request $request, Closure $next)
    {
        // cek guard admin
        if (Auth::guard('admin')->check()) {

            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
