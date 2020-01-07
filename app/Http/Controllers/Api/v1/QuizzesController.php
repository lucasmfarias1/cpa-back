<?php

namespace App\Http\Controllers\Api\v1;

use App\Quiz;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuizRequest;
use App\Repositories\QuizRepository;

class QuizzesController extends Controller
{
    public function index()
    {
        $request = request()->all();
        // TODO
        // $itemsPerPage =
        if ($request['mustSort']) {
            $quizzes = Quiz::paginate($request['itemsPerPage']);
        } else {
            $quizzes = Quiz::orderBy($request['sortBy'][0])
                ->paginate($request['itemsPerPage']);
        }

        return response()->json(['quizzes' => $quizzes], 200);
    }

    public function store(QuizRequest $request)
    {
        $quiz = QuizRepository::create($request);

        return response()->json(['quiz' => $quiz], 201);
    }

    public function show(Quiz $quiz)
    {
        return response()->json(['quiz' => $quiz], 200);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $quiz->update($request->all());

        return response()->json(['quiz', $quiz], 200);
    }

    public function destroy(Quiz $quiz)
    {
        QuizRepository::delete($quiz);

        return response()->json(['message' => 'Quiz deleted'], 200);
    }
}
