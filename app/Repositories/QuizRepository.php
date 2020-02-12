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
                return [
                    "questionId" => $question->id,
                    "questionBody" => $question->body,
                    "questionResults" => [
                        "disagree" => $relevantAnswers
                            ->where('value', 1)->count() / $answerCount,
                        "disagree_partial" => $relevantAnswers
                            ->where('value', 2)->count() / $answerCount,
                        "neutral" => $relevantAnswers
                            ->where('value', 3)->count() / $answerCount,
                        "agree_partial" => $relevantAnswers
                            ->where('value', 4)->count() / $answerCount,
                        "agree" => $relevantAnswers
                            ->where('value', 5)->count() / $answerCount
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
