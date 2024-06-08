<?php

namespace App\Http\Middleware;

use App\Services\Constant\Employee\EmployeeUserRole;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
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
            errUnauthorized('Your token has expired. Please, login again.');
        } 
        catch (Exception $e) {
            errUnauthorized('Your token is invalid. Please, login again.');
        }
        catch (Exception $e) {
            errUnauthorized('Please, attach a Bearer Token to your request');
        }
        
        if ($user && in_array($parseRole, $roles) && !$user->employee->isResign) {
            return $next($request);
        }
        return errUnauthorized();
    }

}
