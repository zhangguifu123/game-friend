<?php

namespace App\Http\Controllers;

use App\Models\GameCollection;
use App\Models\Manager\Game;
use App\Models\PostCollection;
use App\Models\User\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    //
    //发布
    public function addGameCollection(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataGameHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $game = Game::query()->find('game_id');
        if (!$game){
            return msg(11, __LINE__);
        }
        $gameCollection = new GameCollection($data);
        if ($gameCollection->save()) {
            $game->increment('collection_count');
            return msg(0,__LINE__);
        }
        //未知错误
        return msg(4, __LINE__);
    }
    /** 拉取列表信息 */
    public function getGameCollectionList(Request $request)
    {
        if (!$request->route('id')) {
            return msg(3 , __LINE__);
        }
        $worker   = GameCollection::query()->where('uid', $request->route('id'))->get()->toArray();
        $workerIds = [];
        foreach ($worker as $value){
            $workerIds[] = $value['game_id'];
        }
        $gameList = Game::query()->whereIn('games.id',$workerIds)->get()->toArray();
        return msg(0, $gameList);
    }

    /** 删除 */
    public function deleteGameCollection(Request $request)
    {
        if (!$request->route('id') || !$request->input('gameId')) {
            return msg(3 , __LINE__);
        }
        $game = Game::query()->find($request->input('gameId'));
        if (!$game){
            return msg(11, __LINE__);
        }
        $gameCollection = GameCollection::query()->find($request->route('id'));
        if (!$gameCollection){
            return msg(11, __LINE__);
        }
        $game->decrement('collection_count');
        $gameCollection->delete();
        return msg(0, __LINE__);
    }

    //发布
    public function addPostCollection(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataPostHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $post = Post::query()->find($data['postId']);
        if (!$post){
            return msg(11, __LINE__);
        }
        $postCollection = new PostCollection($data);
        if ($postCollection->save()) {
            $post->increment('collection_count');
            return msg(0,__LINE__);
        }
        //未知错误
        return msg(4, __LINE__);
    }
    /** 拉取列表信息 */
    public function getPostCollectionList(Request $request)
    {
        if (!$request->route('id')) {
            return msg(3 , __LINE__);
        }
        $post   = PostCollection::query()->where('uid', $request->route('id'))->get()->toArray();
        $postIds = [];
        foreach ($post as $value){
            $postIds[] = $value['post_id'];
        }
        $gameList = Post::query()->whereIn('posts.id',$postIds)->get()->toArray();
        return msg(0, $gameList);
    }

    /** 删除 */
    public function deletePostCollection(Request $request)
    {
        if (!$request->route('id') || !$request->input('postId')) {
            return msg(3 , __LINE__);
        }
        $post = Post::query()->find($request->input('postId'));
        if (!$post){
            return msg(11, __LINE__);
        }
        $postCollection = PostCollection::query()->find($request->route('id'));
        if (!$postCollection){
            return msg(11, __LINE__);
        }
        $post->decrement('collection_count');
        $postCollection->delete();

        return msg(0, __LINE__);
    }

    //检查函数
    private function _dataGameHandle(Request $request){
        //声明理想数据格式
        $mod = [
            "gameId"   => ["string"],
            "uid"      => ["string"],
        ];
        //是否缺失参数
        if (!$request->has(array_keys($mod))){
            return msg(1,__LINE__);
        }
        //提取数据
        $data = $request->only(array_keys($mod));
        //判断数据格式
        if (Validator::make($data, $mod)->fails()) {
            return msg(3, '数据格式错误' . __LINE__);
        };
        return $data;
    }
    //检查函数
    private function _dataPostHandle(Request $request){
        //声明理想数据格式
        $mod = [
            "postId"   => ["string"],
            "uid"      => ["string"],
        ];
        //是否缺失参数
        if (!$request->has(array_keys($mod))){
            return msg(1,__LINE__);
        }
        //提取数据
        $data = $request->only(array_keys($mod));
        //判断数据格式
        if (Validator::make($data, $mod)->fails()) {
            return msg(3, '数据格式错误' . __LINE__);
        };
        return $data;
    }
}
