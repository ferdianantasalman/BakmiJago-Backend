<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;


class UserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response

    public function handle(Request $request, Closure $next, ...$role): Response
    {
        if (Auth::check() && Auth::user()->role->name == $role) {
            return $next($request);
        }

        return response()->json("You don't have permission to acces this page Hehe!");

        // return $next($request);
    }
}
