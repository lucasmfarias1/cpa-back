<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use App\Course;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required'
        ]);

        $credentials = $request->only('cpf');
        $user = User::where('cpf', $credentials['cpf'])->first();

        if ($user) {
            $token = $this->guard()->login($user);
            return $this->respondWithToken($token);
        } else {
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'GET',
                "https://fatecrl.edu.br/std/alunos/{$credentials['cpf']}"
            );
            $response = $response->getBody()->getContents();
            $response = json_decode($response);

            if (array_key_exists(0, $response)) $legacyUser = $response[0];

            if (isset($legacyUser) && $legacyUser->cd_Cpf) {
                $course = Course::where('shorthand', $legacyUser->sg_Curso)
                    ->first();
                $newUser = User::create([
                    'name' => $legacyUser->nm_Usuario,
                    'cpf' => $legacyUser->cd_Cpf,
                    'email' => $legacyUser->ds_Email,
                    'birthdate' => $legacyUser->dt_Nascimento,
                    'course_id' => $course->id,
                    'id_legacy' => $legacyUser->cd_Usuario,
                    'password' => bcrypt(Str::random(10))
                ]);

                $token = $this->guard()->login($newUser);
                return $this->respondWithToken($token);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }
        // if ($token = $this->guard()->attempt($credentials)) {
        //     return $this->respondWithToken($token);
        // }
    }

    public function me()
    {
        return response()->json($this->guard()->user()->load('course'));
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'user'         => $this->guard()->user()->load('course'),
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
