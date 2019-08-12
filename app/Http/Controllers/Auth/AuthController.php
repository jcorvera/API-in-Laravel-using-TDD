<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller{

    public function store(Request $request){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $user = User::where('email',$request->email)->get();
            $token = $user->createToken($request->email)->accessToken;

            return response()->json(['token'=>$token]);
        }
        // else{
            // return response()->json(['token'=>$token]);
        // }

    }
}
