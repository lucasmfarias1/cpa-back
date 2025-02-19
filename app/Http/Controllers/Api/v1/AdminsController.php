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
        $this->middleware('admin_only');
        $this->middleware('master_only');
    }

    public function index()
    {
        $request = request()->all();

        $itemsPerPage = array_key_exists('itemsPerPage', $request) ?
            $request['itemsPerPage'] :
            '10';
        $sortBy = array_key_exists('sortBy', $request) ?
            $request['sortBy'][0] :
            'name';
        $sortDesc = array_key_exists('sortDesc', $request) ?
            $request['sortDesc'][0] == 'true' ? 'DESC' : 'ASC' :
            'DESC';

        $admins = User::orderBy($sortBy, $sortDesc)
            ->where('is_admin', true)
            ->paginate($itemsPerPage);

        return response()->json(['admins' => $admins], 200);
    }

    public function store(AdminRequest $request)
    {
        $admin = new User($request->validated());
        $admin->password = bcrypt($admin->password);
        $admin->is_admin = true;
        $admin->save();

        return response()->json([
            'message' => 'Admin account created successfully',
            'admin' => $admin
        ], 200);
    }

    public function show(User $admin)
    {
        return response()->json(['admin' => $admin], 200);
    }

    public function update(AdminRequest $request, User $admin)
    {
        $attributes = $request->validated();
        if ($request->has('password'))
            $attributes['password'] = bcrypt($attributes['password']);

        $admin->update($attributes);

        return response()->json([
            'message' => 'Admin account updated successfully',
            'admin' => $admin
        ], 200);
    }

    public function destroy(User $admin)
    {
        if ($admin->is_master) {
            return response()->json([
                'message' => 'Master admin cannot be deleted'
            ], 403);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin account deleted successfully'
        ], 200);
    }
}
