<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Farmer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        session()->has('user_type') ?: session(['user_type' => Auth::user()->getUserType()]);
        if (session('user_type') == 'FARMER') {
            return $next($request);
        } elseif (session('user_type') == 'ADMIN') {
            return redirect()->route('admins.dashboard');
        } else {
            return redirect()->route('advisor.dashboard');
        }

        return $next($request);
    }
}
