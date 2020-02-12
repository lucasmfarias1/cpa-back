<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MasterOnly
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();

        if (!$user->is_master || !$user->is_admin) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, user must be master admin'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
