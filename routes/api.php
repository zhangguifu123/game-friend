<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Manager\MangagerController;
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

Route::namespace('Api')->group(function (){
    Route::post('/login',[MangagerController::class, 'check']);
    /**Game */
    Route::post('/test','Game\GameController@publish');
        Route::group(['middleware' => 'manager.check'], function () {
            //Game增删改查
            Route::post('/game/add',[GameController::class, 'publish']);
            Route::get('/game/me/{uid}','Game\GameController@get_me_list');
            Route::get('/game/like/{uid}','Game\GameController@get_like_list');
            Route::get('/game/collection/{uid}','Game\GameController@get_collection_list');
        });

});
