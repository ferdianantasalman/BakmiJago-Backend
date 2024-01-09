<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHas3Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role1, $role2, $role3) : Response
    {
        if (Auth::user()->role->name == $role1 || Auth::user()->role->name == $role2 || Auth::user()->role->name == $role3) {
            return $next($request);
        }

        return response()->json("You don't have permission to acces this page!");
    }
}
