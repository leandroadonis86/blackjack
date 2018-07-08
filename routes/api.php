<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('users/emailavailable', 'UserControllerAPI@emailAvailable');
Route::get('users/nicknameavailable', 'UserControllerAPI@nicknameAvailable');

Route::get('users', 'UserControllerAPI@getUsers');
Route::get('users/{id}', 'UserControllerAPI@getUser');
Route::post('users', 'UserControllerAPI@store');
Route::put('users/{id}', 'UserControllerAPI@update');
Route::delete('users/{id}', 'UserControllerAPI@delete');

Route::put('users/block/{id}', 'UserControllerAPI@blockUser');
Route::put('users/unblock/{id}', 'UserControllerAPI@unblockUser');

Route::get('user/email/{email}', 'UserControllerAPI@getUserByEmail');
Route::post('user/login', 'UserControllerAPI@loginUser');
Route::post('user/add', 'UserControllerAPI@addUser');
Route::put('user/mng/{id}', 'UserControllerAPI@updateManagement');
Route::put('user/adm/rst/pass/{id}', 'UserControllerAPI@admRstPassManagement');

Route::put('user/score/{id}', 'UserControllerAPI@updateScore');

Route::get('games', 'GameControllerAPI@index');
Route::get('games/lobby', 'GameControllerAPI@lobby');
Route::get('games/status/{status}', 'GameControllerAPI@gamesStatus');
Route::get('games/{id}', 'GameControllerAPI@getGame');
Route::post('games', 'GameControllerAPI@store');
Route::patch('games/{id}/join-start', 'GameControllerAPI@joinAndStart');
Route::patch('games/{id}/endgame/{winner}', 'GameControllerAPI@endgame');

Route::put('platform/mail/{email}', 'PlatformControllerAPI@updateEmail');

Route::get('statistics', 'StatisticsControllerAPI@generalStats');
Route::get('statistics/{id}', 'StatisticsControllerAPI@userStats');

