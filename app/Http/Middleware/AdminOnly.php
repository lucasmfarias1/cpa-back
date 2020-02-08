<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();
        if (!$user->is_admin) {
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
