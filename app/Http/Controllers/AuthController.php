<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public  function  register(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',

        ]);
        $user= User::create(array_merge(
            $request->except('password'),
            [
                'password' => bcrypt($request->password),
            ]
        ));

        return response()->json($user);
    }
    public  function  login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $user = Auth::user();

            $tokenResult = $user->createToken('authapi');

            return response()->json([
                'token' => $tokenResult->accessToken,
            ]);
        } else {
            return response()->json([
                'message' => "Invalid email or password",
            ]);
        }
    }
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
