<?php

namespace App\Http\Controllers\Api\v1;

use App\Quiz;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuizRequest;
use App\Http\Requests\QuizActivateRequest;
use App\Repositories\QuizRepository;
use Illuminate\Support\Facades\Auth;

class QuizzesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(
            'assure_quiz_pending',
            ['only' => ['update', 'destroy']]
        );
        $this->middleware(
            'admin_only',
            ['except' => ['show', 'check']]
        );
    }

    public function index()
    {
        $request = request()->all();

        $archivedOrNot = array_key_exists('archived', $request) ?
            '=' :
            '!=';
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
            ->where('status', $archivedOrNot, 3)
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

    public function activate(QuizActivateRequest $request, Quiz $quiz)
    {
        if ($quiz->status != 0) {
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

        $quiz->update([
            'status'   => 1,
            'deadline' => $request->input('deadline')
        ]);
        return response()->json([
            'quiz' => $quiz,
            'message' => "Quiz #{$quiz->id} activated"
        ], 200);
    }

    public function finish(Quiz $quiz)
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

        $quiz->update(['status' => 2]);
        return response()->json([
            'quiz' => $quiz,
            'message' => "Quiz #{$quiz->id} finished"
        ], 200);
    }

    public function check(Quiz $quiz)
    {
        $canAnswerQuiz = !Auth::guard()->user()->answeredQuiz($quiz->id);

        if ($canAnswerQuiz) {
            return response()->json([
                "message" => "This user has not already answered this Quiz"
            ], 200);
        } else {
            return response()->json([
                "message" => "This user has already answered this Quiz"
            ], 403);
        }
    }

    public function archive(Quiz $quiz)
    {
        if ($quiz->status != 2 && $quiz->status != 3) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz status must be 2 (encerrado) or 3 (arquivado)'
                        ]
                    ]
                ],
                403
            );
        }

        $quiz->status == 2 ?
            $quiz->update(['status' => 3]) :
            $quiz->update(['status' => 2]);
        return response()->json([
            'quiz' => $quiz,
            'message' => "Quiz #{$quiz->id} archived"
        ], 200);
    }

    public function results(Request $request, Quiz $quiz)
    {
        if ($quiz->status != 2 && $quiz->status != 3) {
            return response()->json(
                [
                    'errors' => [
                        'status' => [
                            'Forbidden, quiz status must be 2 (encerrado) or 3 (arquivado)'
                        ]
                    ]
                ],
                403
            );
        }

        $quizResults = QuizRepository::results($request, $quiz);

        return response()->json([
            'quiz'    => $quiz,
            'results' => $quizResults
        ], 200);
    }
}
