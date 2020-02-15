<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserCanAnswerQuiz
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
            $user->answeredQuiz($quiz->id) ||
            $this->courseCheck($user, $quiz)) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, the user cannot answer this quiz'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }

    private function courseCheck($user, $quiz)
    {
        return (
            $quiz->course_name != 'TODOS' &&
            $quiz->course_id != $user->course->id);
    }
}
