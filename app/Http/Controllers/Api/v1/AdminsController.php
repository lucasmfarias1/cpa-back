<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminRequest;

class AdminsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(AdminRequest $request)
    {
        $admin = new User($request->validated());
        $admin->password = bcrypt($admin->password);
        $admin->is_admin = true;
        $admin->save();

        return response()->json([
            'message' => 'Admin account created successfully'
        ], 200);
    }
}
