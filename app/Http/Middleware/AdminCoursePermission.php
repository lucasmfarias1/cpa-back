<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminCoursePermission
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();
        $quiz = $request->quiz;

        if (!$user->is_master &&
            $user->course->id != $quiz->course_id) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, admin must be master or admin of this course'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
