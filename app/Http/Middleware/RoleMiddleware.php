<?php

namespace App\Http\Middleware;

use App\Services\Constant\Employee\EmployeeUserRole;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {   
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
            $parseRole = EmployeeUserRole::display($user->role);
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return errUnauthorized("Token is invalid");
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return errUnauthorized("Token is expired");
            }else{
                return errUnauthorized("Bearer token not found");;
            }
        }
        
        if ($user && in_array($parseRole, $roles) && !$user->employee->isResign) {
            return $next($request);
        }
        return errUnauthorized('Employee already resign or dont have the permission');
    }

}
