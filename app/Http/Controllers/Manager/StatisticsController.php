<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager\Game;
use App\Models\User;
use App\Models\User\Post;
use \Redis;
use \Exception;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    //
    public function statistics(Request $request) {
        $game = Game::all();
        $gameCount = $game->count();
        $gameCollectionCounts = $game->sum('collections');

        $userCount = User::all()->count();

        $post = Post::all();
        $postCount = $post->count();
        $postCollectionCount = $post->sum('collections');
        $postViewCounts = $post->sum('views');

        $count['game']['gameCount'] = $gameCount;
        $count['game']['gameCollectionCounts'] = $gameCollectionCounts;
        $count['user']['userCount'] = $userCount;
        $count['post']['postCount'] = $postCount;
        $count['post']['postCollectionCount'] = $postCollectionCount;
        $count['post']['postViewCounts'] = $postViewCounts;

        return msg(0, $count);
    }

    public function setData(Request $request) {
        // 如果redis连接失败 中止保存
        $gameId = $request->input('gameId');
        $subject = $request->input('subject');
        $openid = $request->input('openid');
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }
        $redis->hSet("gameData", $openid, $gameId . '_' . $subject);
        $redis->hSet("studyData", $openid, $gameId . '_' . $subject);
    }
}
