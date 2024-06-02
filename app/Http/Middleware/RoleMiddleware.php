<?php

namespace App\Http\Middleware;

use App\Models\Employee\EmployeeUser;
use App\Services\Constant\Employee\EmployeeUserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::user() == null || EmployeeUserRole::display(Auth::user()->role) != $role) {
            errUnauthorized();
        }
        return $next($request);
    }
}
