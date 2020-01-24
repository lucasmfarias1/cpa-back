<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserHasCompletedProfile
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();
        if (!$user->has_completed_profile) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, user must have a completed profile'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
