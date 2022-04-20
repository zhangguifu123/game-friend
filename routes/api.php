<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Manager\MangagerController;
use App\Http\Controllers\User\FriendGroupController;
use App\Http\Controllers\User\FriendController;
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
    Route::post('/manager/login',[MangagerController::class, 'check']);
    //图片上传
    Route::post('/image', [ImageController::class, 'upload']);
    //群聊
    Route::post('/group/{uid}', [FriendGroupController::class, 'make']);
    Route::get('/group/me/{uid}', [FriendGroupController::class, 'getMeList']);
    Route::get('/group/join/{uid}', [FriendGroupController::class, 'getJoinList']);
    Route::post('/group/add/{uid}', [FriendGroupController::class, 'add']);
    Route::delete('/group/{uid}', [FriendGroupController::class, 'deleteGroup']);
    Route::delete('/group/person/{uid}', [FriendGroupController::class, 'delete']);
    //反馈Tip
    Route::get('/tip/list/{page}',[GameController::class, 'getList']);
    Route::post('/tip/add',[GameController::class, 'publish']);
    Route::delete('/tip/{id}',[GameController::class, 'delete']);
    Route::put('/tip/{id}',[GameController::class, 'update']);
    /**Game */
    Route::get('/game/list/{page}',[GameController::class, 'getList']);
    Route::group(['middleware' => 'manager.check'], function () {
        //Game增删改查
        Route::post('/game/add',[GameController::class, 'publish']);
	    Route::delete('/game/{id}',[GameController::class, 'delete']);
        Route::put('/game/{id}',[GameController::class, 'update']);
        Route::get('/game/me/{uid}','Game\GameController@get_me_list');
        Route::get('/game/like/{uid}','Game\GameController@get_like_list');
        Route::get('/game/collection/{uid}','Game\GameController@get_collection_list');
    });

});
