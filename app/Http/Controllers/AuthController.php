<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        // $user = new \App\User;
        // $user->name = 'Manzano';
        // $user->email = 'test@test';
        // $user->ra = '123';
        // $user->cpf = '425';
        // $user->password = bcrypt('123456');
        // $user->save();

        $request->validate([
            'ra'  => 'required',
            'cpf' => 'required'
        ]);

        $credentials = $request->only('ra', 'cpf');
        $user = User::where('ra', $credentials['ra'])->first();

        if ($user && $user->cpf === $credentials['cpf']) {
            $token = $this->guard()->login($user);
            return $this->respondWithToken($token);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

    }

    public function me()
    {
        return response()->json($this->guard()->user());
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'user'         => $this->guard()->user(),
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }
}
