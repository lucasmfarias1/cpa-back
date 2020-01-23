<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update(Request $request, User $user)
    {
        if ($user != Auth::guard()->user()) {
            return response()->json([
                'message' => 'User cannot be edited by another user'
            ], 403);
        }

        $request->validate([
            'term' => ['required', 'integer', 'min:1', 'max:6'],
            'sex' =>  ['required', 'in:male,female'],
            // 'email' =>  ['required', 'email'],
            // 'birthdate' => ['required', 'date', 'before:today']
        ]);

        $user->update([
            'term' => $request->input('term'),
            'sex'  => $request->input('sex')
            // 'email' => $user->email ? $user->email : $request->input('email'),
            // 'birthdate' => $user->birthdate ? $user->birthdate : $request->input('birthdate'),
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ], 200);
    }
}
