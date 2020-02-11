<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();
        $isAdminOrNull = $request->admin ? $request->admin->is_admin : true;

        if (!$user->is_admin || !$isAdminOrNull) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, user must be admin'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
