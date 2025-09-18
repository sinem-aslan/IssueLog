<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->is_active == 0) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Sistemde şu anda aktif değilsiniz, yönetici ile iletişime geçiniz.'
            ]);
        }
        return $next($request);
    }
}
