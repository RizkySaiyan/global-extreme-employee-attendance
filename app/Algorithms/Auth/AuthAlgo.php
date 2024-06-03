<?php

namespace App\Algorithms\Auth;

use App\Models\Employee\EmployeeUser;
use App\Services\Constant\Employee\EmployeeUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthAlgo
{

    public function login(Request $request)
    {
        
        try {
            $tokenPair = DB::transaction(function() use($request){
                $credentials = $request->only(['email', 'password']);
                
                if(!$token = auth()->attempt($credentials)){
                    errInvalidCredentials('Wrong email or password');
                }
                
                $user = EmployeeUser::where('email',$credentials['email'])->first();
                $data = [
                    'employeeId' => $user->employeeId,
                    'employeeName' => $user->employee->name,
                    'email' => $user->email,
                    'role' => EmployeeUserRole::display($user->role),
                    'accessToken' => $token,
                    'expiresIn' => auth()->factory()->getTtl() * 60
                ];
                return $data;
            });
            return success($tokenPair);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}
