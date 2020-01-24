<?php

namespace App\Http\Controllers\Api\v1;

use App\AnswerCard;
use App\Quiz;
use App\Repositories\AnswerCardRepository;
use App\Http\Requests\AnswerCardRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnswerCardsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('check_user_answered_quiz');
        $this->middleware('check_user_has_completed_profile');
    }

    public function index()
    {
        //
    }

    public function create(Quiz $quiz)
    {
        if ($quiz->status != 1) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz status must be 1 (ativo)'
                        ]
                    ]
                ],
                403
            );
        }

        return response()->json(['quiz' => $quiz]);
    }

    public function store(AnswerCardRequest $request, Quiz $quiz)
    {
        if ($quiz->status != 1) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz status must be 1 (ativo)'
                        ]
                    ]
                ],
                403
            );
        }

        // answercard repository to store the validated answercard now
        $answerCard = AnswerCardRepository::create($request, $quiz);
        return response()->json([
                "message" => "AnswerCard stored successfully"
            ], 200);
    }

    public function show(AnswerCard $answerCard)
    {
        //
    }

    public function edit(AnswerCard $answerCard)
    {
        //
    }
    public function update(Request $request, AnswerCard $answerCard)
    {
        //
    }
    public function destroy(AnswerCard $answerCard)
    {
        //
    }
}
