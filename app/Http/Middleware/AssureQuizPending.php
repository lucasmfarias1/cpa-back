<?php

namespace App\Http\Middleware;

use Closure;

class AssureQuizPending
{
    public function handle($request, Closure $next)
    {
        $quiz = $request->quiz;
        if ($quiz->status !== 0) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz status must be 0 (pendente)'
                        ]
                    ]
                ],
                403
            );
        }

        return $next($request);
    }
}
