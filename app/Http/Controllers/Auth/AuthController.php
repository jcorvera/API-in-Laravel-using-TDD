<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller{

    public function store(Request $request)
    {
        $token = null;
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            $token = User::whereEmail($request->email)->first()->createToken($request->email)->accessToken;
            return response()->json(['token'=>$token, 'user'=> $token]);
        }else
        {
            return response()->json(['token'=>$token]);
        }

    }

    public function redirect(String $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(String $provider)
    {
        $user = Socialite::driver($provider)->user();
        \Log::info('user',[$user]);
        \Log::info($user->token);
        return redirect()->away("http://localhost:8000?token=$user->token");
    }
}
