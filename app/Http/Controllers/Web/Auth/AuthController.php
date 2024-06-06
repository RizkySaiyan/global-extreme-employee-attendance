<?php

namespace App\Http\Controllers\Web\Auth;

use App\Algorithms\Auth\AuthAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function login(AuthRequest $request){
        $algo = new AuthAlgo();

        return $algo->login($request);
    }

    public function me(){
        $algo = new AuthAlgo();

        return $algo->getAuthenticatedUser();
    }
}
