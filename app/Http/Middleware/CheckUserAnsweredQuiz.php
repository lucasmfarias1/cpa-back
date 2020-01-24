<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserAnsweredQuiz
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::guard()->user();
        $quiz = $request->quiz;
        if (!$quiz ||
            $user->answeredQuiz($quiz->id)) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, the user has already answered this quiz'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
