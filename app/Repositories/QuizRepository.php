<?php

namespace App\Repositories;

use App\Quiz;
use App\Question;
use App\AnswerCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizRepository
{
    public static function create($request)
    {
        DB::beginTransaction();

        try {
            $currentUser = Auth::guard()->user();
            $quiz = new Quiz($request->validated());

            if (!$currentUser->is_master)
                $quiz->course_id = $currentUser->course->id;

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
            $currentUser = Auth::guard()->user();
            $quiz->fill($request->validated());

            if (!$request->has('course_id')) $quiz->course_id = null;
            if (!$currentUser->is_master)
                $quiz->course_id = $currentUser->course->id;

            $quiz->save();

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
            if ($answerCount == 0) return [];

            $answerCards = AnswerCard::where('quiz_id', $quiz->id);

            if ($request->has('filters')) {
                $filters = $request->input('filters');
                if (array_key_exists('courses', $filters)
                    && !empty($filters['courses']))
                    $answerCards->whereIn('course_id', $filters['courses']);

                if (array_key_exists('terms', $filters)
                    && !empty($filters['terms']))
                    $answerCards->whereIn('term', $filters['terms']);

                if (array_key_exists('sexes', $filters)
                    && !empty($filters['sexes']))
                    $answerCards->whereIn('sex', $filters['sexes']);

                if (array_key_exists('max_age', $filters)
                    && !empty($filters['max_age']))
                    $answerCards->where('age', '<=', $filters['max_age']);

                if (array_key_exists('min_age', $filters)
                    && !empty($filters['min_age']))
                    $answerCards->where('age', '>=', $filters['min_age']);
            }

            $answerCards = $answerCards->get();

            $answers = collect([]);
            foreach ($answerCards as $answerCard) {
                $answers = $answers->concat($answerCard->answers);
            }

            $quizResults = $quiz->questions
                ->map(function($question) use ($answerCount, $answers) {
                $relevantAnswers = $answers
                    ->where('question_id', $question->id);

                $relevantAnswerCount = array_map(function($n) use ($relevantAnswers) {
                    return $relevantAnswers->where('value', $n)->count();
                }, [1, 2, 3, 4, 5]);
                return [
                    "questionId" => $question->id,
                    "questionBody" => $question->body,
                    "questionResults" => [
                        "disagree" => [
                            "percent" => $relevantAnswerCount[0] / $answerCount,
                            "count"   => $relevantAnswerCount[0]
                        ],

                        "disagree_partial" => [
                            "percent" => $relevantAnswerCount[1] / $answerCount,
                            "count"   => $relevantAnswerCount[1]
                        ],

                        "neutral" => [
                            "percent" => $relevantAnswerCount[2] / $answerCount,
                            "count"   => $relevantAnswerCount[2]
                        ],

                        "agree_partial" => [
                            "percent" => $relevantAnswerCount[3] / $answerCount,
                            "count"   => $relevantAnswerCount[3]
                        ],

                        "agree" => [
                            "percent" => $relevantAnswerCount[4] / $answerCount,
                            "count"   => $relevantAnswerCount[4]
                        ],
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
