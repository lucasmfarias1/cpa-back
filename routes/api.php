<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// v1 API
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function($router) {
    Route::group([
        'prefix' => 'auth'
    ], function($router) {
        Route::post('login', 'Api\v1\AuthController@login');
        Route::post('logout', 'Api\v1\AuthController@logout');
        Route::post('refresh', 'Api\v1\AuthController@refresh');
        Route::post('me', 'Api\v1\AuthController@me');
    });

    // Home
    Route::get('active-quizzes', 'Api\v1\HomeController@index');

    // Quizzes
    Route::resource('quizzes', 'Api\v1\QuizzesController');
    Route::post('quizzes/{quiz}/activate', 'Api\v1\QuizzesController@activate');
    Route::get('quizzes/{quiz}/check', 'Api\v1\QuizzesController@check');
    Route::post('quizzes/{quiz}/finish', 'Api\v1\QuizzesController@finish');

    // AnswerCards
    Route::get(
        'quizzes/{quiz}/answers/create',
        'Api\v1\AnswerCardsController@create'
    );
    Route::post('quizzes/{quiz}/answers', 'Api\v1\AnswerCardsController@store');

    // Users
    Route::post('users/{user}', 'Api\v1\UsersController@update');
});

