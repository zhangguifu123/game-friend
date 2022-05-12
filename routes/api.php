<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Manager\MangagerController;
use App\Http\Controllers\User\FriendGroupController;
use App\Http\Controllers\User\FriendController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\PostController;
use App\Http\Controllers\Game\CommentController;
use App\Http\Controllers\Game\ReplyController;
use App\Http\Controllers\TipController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\Manager\StatisticsController;
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
    //统计
    Route::post('/test', [StatisticsController::class, 'test']);
    Route::get('/viewShow', [StatisticsController::class, 'videoShow']);
    Route::get('/setData', [StatisticsController::class, 'setData']);
    Route::get('/statistics', [StatisticsController::class, 'statistics']);
    //Banner
    Route::post('/banner/upload',[BannerController::class, 'upload']);
    Route::get('/banner/list/{page}',[BannerController::class, 'getList']);
    Route::post('/manager/login',[MangagerController::class, 'check']);


    Route::post('/login',[UserController::class, 'login']);
    Route::post('/check',[UserController::class, 'check']);
    Route::get('/user/{id}',[UserController::class, 'getOneUser']);

    Route::post('/avatar',[AvatarController::class, 'upload']);
    Route::post('/authenticate',[UserController::class, 'authenticate']);
    //Notice
    Route::get('/notice/me/{id}',[NoticeController::class, 'getList']);
    Route::post('/notice/add',[NoticeController::class, 'publish']);
    Route::delete('/notice/{id}',[NoticeController::class, 'delete']);
    Route::put('/notice/{id}',[NoticeController::class, 'update']);
    //收藏
    Route::get('/collection/post/{id}',[CollectionController::class, 'getPostCollectionList']);
    Route::get('/collection/game/{id}',[CollectionController::class, 'getGameCollectionList']);
    Route::post('/collection/post',[CollectionController::class, 'addPostCollection']);
    Route::post('/collection/game',[CollectionController::class, 'addGameCollection']);
    Route::delete('/collection/post/{id}',[CollectionController::class, 'deletePostCollection']);
    Route::delete('/collection/game/{id}',[CollectionController::class, 'deleteGameCollection']);
    //征友贴评论区
    Route::post('/posts/{id}/comment',[CommentController::class, 'publish']);
    Route::delete('/posts/comment/{id}',[CommentController::class, 'delete']);
    Route::get('/posts/{id}/comment',[CommentController::class, 'getList']);
    Route::post('/posts/comment/{id}/reply',[ReplyController::class, 'publish']);
    Route::delete('/posts/comment/{id}/reply',[ReplyController::class, 'delete']);
    Route::get('/posts/comment/{id}/reply',[ReplyController::class, 'getList']);
    //征友贴
    Route::post('/posts/add',[PostController::class, 'publish']);
    Route::delete('/posts/{id}',[PostController::class, 'delete']);
    Route::put('/posts/{id}',[PostController::class, 'update']);
    Route::get('/posts/{id}',[PostController::class, 'addView']);
    Route::get('/posts/list/{page}',[PostController::class, 'getList']);
    //图片上传
    Route::post('/image', [ImageController::class, 'upload']);
    //添加好友、删除好友
    Route::post('/friend/add/{uid}', [FriendController::class, 'add']);
    Route::delete('/friend/del/{uid}', [FriendController::class, 'delete']);
    Route::get('/friend/me/{uid}', [FriendController::class, 'getMeList']);
    Route::get('/friend/search', [FriendController::class, 'search']);
    Route::get('/friend/notice/{uid}', [FriendController::class, 'getNotice']);
    Route::put('/friend/{uid}', [FriendController::class, 'updateStatus']);
    //群聊
    Route::post('/group/{uid}', [FriendGroupController::class, 'make']);
    Route::get('/group/{groupId}', [FriendGroupController::class, 'getOneGroup']);
    Route::get('/group/me/{uid}', [FriendGroupController::class, 'getMeList']);
    Route::get('/group/join/{uid}', [FriendGroupController::class, 'getJoinList']);
    Route::post('/group/add/{uid}', [FriendGroupController::class, 'add']);
    Route::delete('/group/del/{uid}', [FriendGroupController::class, 'deleteGroup']);
    Route::delete('/group/person/del/{uid}', [FriendGroupController::class, 'delete']);
    //反馈Tip
    Route::get('/tip/list/{page}',[TipController::class, 'getList']);
    Route::post('/tip/add',[TipController::class, 'publish']);
    Route::delete('/tip/{id}',[TipController::class, 'delete']);
    Route::put('/tip/{id}',[TipController::class, 'update']);
    /**Game */
    Route::post('/game/list/{page}',[GameController::class, 'getList']);
    Route::group(['middleware' => 'manager.check'], function () {
        Route::post('/manager/add',[MangagerController::class, 'add']);
        Route::delete('/manager/{id}',[MangagerController::class, 'delete']);
        Route::get('/manager/list',[MangagerController::class, 'getList']);
        Route::put('/{id}/update',[UserController::class, 'update']);
        Route::get('/user/list/{page}',[UserController::class, 'showUser']);
        //Game增删改查
        Route::post('/game/add',[GameController::class, 'publish']);
	    Route::delete('/game/{id}',[GameController::class, 'delete']);
        Route::put('/game/{id}',[GameController::class, 'update']);
        Route::get('/game/me/{uid}','Game\GameController@get_me_list');
        Route::get('/game/like/{uid}','Game\GameController@get_like_list');
        Route::get('/game/collection/{uid}','Game\GameController@get_collection_list');
    });

});
