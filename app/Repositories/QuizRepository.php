<?php

namespace App\Repositories;

use App\Quiz;
use App\Question;
use Illuminate\Support\Facades\DB;

class QuizRepository
{
    public static function create($request)
    {
        DB::beginTransaction();

        try {
            $quiz = new Quiz($request->validated());
            $quiz->status = 0;
            $quiz->save();

            foreach ($request->input('questions') as $question) {
                Question::create([
                    "body" => $question['body'],
                    "quiz_id" => $quiz->id
                ]);
            }

            DB::commit();
            return $quiz;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public static function update($request, $quiz)
    {
        DB::beginTransaction();

        try {
            $quiz->update($request->validated());
            $quiz->questions()->delete();
            foreach ($request->input('questions') as $question) {
                $quiz->questions()->create([
                    "body" => $question['body']
                ]);
            }

            DB::commit();
            return $quiz;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public static function delete($quiz)
    {
        DB::beginTransaction();

        try {
            $quiz->delete();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public static function results($request, $quiz)
    {
        DB::beginTransaction();

        try {
            $answerCount = $quiz->answerCards->count();

            $quizResults = $quiz->questions->map(function($question) use ($answerCount) {
                return [
                    "questionId" => $question->id,
                    "questionBody" => $question->body,
                    "questionResults" => [
                        "disagree" => $question->answers->where('value', 1)->count() / $answerCount,
                        "disagree_partial" => $question->answers->where('value', 2)->count() / $answerCount,
                        "neutral" => $question->answers->where('value', 3)->count() / $answerCount,
                        "agree_partial" => $question->answers->where('value', 4)->count() / $answerCount,
                        "agree" => $question->answers->where('value', 5)->count() / $answerCount
                    ]
                ];
            });

            DB::commit();
            return $quizResults;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}


// {
//     name: "Questão asdqwf dwqkjd dw wdqjwifw wjd dw.",
//     "concordam": "14%",
//     "concordam parcialmente": "14%",
//     "neutros": "14%",
//     "discordam parcialmente": "14%",
//     "discordam": "14%",
// },
