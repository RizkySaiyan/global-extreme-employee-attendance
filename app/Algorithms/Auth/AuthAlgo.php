<?php

namespace App\Algorithms\Auth;

use App\Models\Employee\EmployeeUser;
use App\Parser\Auth\AuthParser;
use App\Services\Constant\Employee\EmployeeUserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthAlgo
{
    public function login(Request $request)
    {
        try {
            $tokenPair = DB::transaction(function () use ($request) {
                $credentials = $request->only(['email', 'password']);

                if (!$token = JWTAuth::attempt($credentials)) {
                    errInvalidCredentials();
                }
                $user = EmployeeUser::where('email', $credentials['email'])->first();
                $data = [
                    'employeeId' => $user->employeeId,
                    'employeeName' => $user->employee->name,
                    'email' => $user->email,
                    'role' => EmployeeUserRole::display($user->role),
                    'accessToken' => $token,
                    'expiresIn' => auth()->factory()->getTtl() * 60,
                ];
                return $data;
            });
            return success($tokenPair);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function getAuthenticatedUser()
    {
        $user = Auth::user();

        return success(AuthParser::getAuthenticatedUser($user));
    }

    public function logout()
    {
        Auth::logout();
        return success();
    }
}
