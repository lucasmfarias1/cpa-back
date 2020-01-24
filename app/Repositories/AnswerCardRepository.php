<?php

namespace App\Repositories;

use App\Question;
use App\AnswerCard;
use App\Answer;
use App\UserQuizAnswered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnswerCardRepository
{
    public static function create($request, $quiz)
    {
        DB::beginTransaction();

        try {
            $user = Auth::guard()->user();

            $answerCard = new AnswerCard();
            $answerCard->quiz_id = $quiz->id;
            $answerCard->course_id = $user->course->id;
            $answerCard->term = $user->term;
            $answerCard->age = $user->age;
            $answerCard->sex = $user->sex;
            $answerCard->save();

            foreach ($request->input('answers') as $answer) {
                $question = Question::findOrFail($answer['question_id']);
                Answer::create([
                    'answer_card_id' => $answerCard->id,
                    'question_id'    => $question->id,
                    'value'          => $answer['value']
                ]);
            }

            UserQuizAnswered::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id
            ]);

            DB::commit();
            return $answerCard;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
