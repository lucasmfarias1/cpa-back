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
            $quiz->questions()->delete();
            $quiz->delete();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
