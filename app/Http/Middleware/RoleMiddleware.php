<?php

namespace App\Http\Middleware;

use App\Services\Constant\Employee\EmployeeUserRole;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
            $parseRole = EmployeeUserRole::display($user->role);
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                errUnauthorized("Token is invalid");
            } else if ($e instanceof TokenExpiredException) {
                errUnauthorized("Token is expired");
            } else {
                errUnauthorized("Bearer token not found");
            }
        }

        if ($user && in_array($parseRole, $roles) && !$user->employee->isResign) {
            return $next($request);
        }
        errUnauthorized('Employee already resign or dont have the permission');
    }

}
