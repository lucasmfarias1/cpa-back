<?php

namespace App\Http\Middleware;

use Closure;

class CheckQuizAvailable
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
        $quiz = $request->quiz;
        if (!$quiz->is_available) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz is not available'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
