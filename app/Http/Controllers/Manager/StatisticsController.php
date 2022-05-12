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
        $redis->hSet("gameData:$openid", $gameId, 1);
        $redis->hSet("studyData", $openid, $subject);
    }

    public function test(Request $request) {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }

        $redis->hSet('gameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '2', 1);
        $redis->hSet('gameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '4', 1);


        $redis->hSet('gameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '4', 1);
        $redis->hSet('gameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '2', 1);

        $redis->hSet('gameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '2', 1);
        $redis->hSet('gameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '7', 1);

        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '2', 1);
        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '7', 1);
        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '4', 1);
        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '8', 1);



    }

    private function show( $data, $firstOpenid, $secondOpenid)
    {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }

        $fm1 = 0;
        $fm2 = 0;
        $fm3 = 0;
        for ( $i = 0; $i < count($data); $i++ )
        {
            $id1_s = $redis->hget( $firstOpenid, $data[$i]);
            if (!$id1_s) {
                $id1_s = 0;
            }
            $id2_S = $redis->hget( $secondOpenid, $data[$i]);
            if (!$id2_S) {
                $id2_S = 0;
            }
            $fm1  += $id1_s * $id1_s;
            $fm2  += $id2_S * $id2_S;
            $fm3  += $id1_s * $id2_S;
        }
        return $fm3 / sqrt($fm1) / sqrt($fm2);
    }

    /**
     * 视频推送
     * @param Request $request
     * @return false|string
     */
    public function videoShow(Request $request)
    {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }

        $masterId = $request['id'];
        $data = array();
        $res  = array();
        for( $userId = 1; $userId <= User::query()->count(); $userId++)
        {
            if($userId != $masterId)
            {
                $diff = $redis->sunion("video:".$masterId,"video:".$userId);
                $data[$userId] = $this->show($diff, $masterId, $userId );
            }
        }
        arsort($data);
        $data = array_keys($data);
        for ( $j = 0; $j < count($data); $j++ )
        {
            $t = User::query()->where('openid',$data[$j])->get()->first();
            array_push($res,$t);
        }
        return msg(0, $res);
    }
}
