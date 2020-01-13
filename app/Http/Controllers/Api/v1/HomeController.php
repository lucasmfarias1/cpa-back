<?php

namespace App\Http\Controllers\Api\v1;

use App\Quiz;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    public function index()
    {
        $activeQuizzes = Quiz::orderBy('deadline', 'ASC')
            ->where('status', 1)
            ->get();

        return response()->json(['quizzes' => $activeQuizzes], 200);
    }
}
