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
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $request = request()->all();
        $itemsPerPage = array_key_exists('itemsPerPage', $request) ?
            $request['itemsPerPage'] :
            '10';
        $sortBy = array_key_exists('sortBy', $request) ?
            $request['sortBy'][0] :
            'name';
        $sortDesc = array_key_exists('sortDesc', $request) ?
            $request['sortDesc'][0] == 'true' ? 'DESC' : 'ASC' :
            'DESC';

        $quizzes = Quiz::orderBy($sortBy, $sortDesc)
            ->paginate($itemsPerPage);

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

    public function update(QuizRequest $request, Quiz $quiz)
    {
        $quiz = QuizRepository::update($request, $quiz);

        return response()->json(['quiz' => $quiz], 200);
    }

    public function destroy(Quiz $quiz)
    {
        QuizRepository::delete($quiz);

        return response()->json(['message' => 'Quiz deleted'], 200);
    }
}
