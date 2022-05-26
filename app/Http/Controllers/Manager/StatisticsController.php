<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager\Game;
use App\Models\StudyInformation;
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
        $check = $redis->hGet("setGameData:$openid", $gameId);
        if (!$check) {
            $redis->hSet("gameData:$openid", $gameId, 1);
            $redis->sAdd("setGameData:$openid", $gameId);
        } else {
            $sum = $check + 1;
            $redis->hSet("gameData:$openid", $gameId, $sum);
        }

        $check = $redis->hGet("subjectData:$openid", $subject);
        if (!$check) {
            $redis->hSet("subjectData:$openid", $subject, 1);
        } else {
            $sum = $check + 1;
            $redis->hSet("subjectData:$openid", $subject, $sum);
        }

        $redis->sAdd("setGameData:", $gameId);

        return msg(0, __LINE__);
    }

    public function test(Request $request) {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }
        $redis->hSet("subjectData:owEKj5QWo6O9JyGB0oRiDRWSPFuc", '数学建模大赛', 2);
        $redis->hSet("subjectData:owEKj5QWo6O9JyGB0oRiDRWSPFuc", '物理竞赛', 3);
        $redis->hSet("subjectData:owEKj5QWo6O9JyGB0oRiDRWSPFuc", '电子竞赛', 4);
//        $redis->hSet('gameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '2', 1);
//        $redis->hSet('gameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '4', 1);
//        $redis->sAdd('setGameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '4');
//        $redis->sAdd('setGameData:' . 'owEKj5QWo6O9JyGB0oRiDRWSPFuc', '2');
//
//        $redis->hSet('gameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '4', 1);
//        $redis->hSet('gameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '2', 1);
//        $redis->sAdd('setGameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '4');
//        $redis->sAdd('setGameData:' . 'owEKj5dCTS4lFsoxzVPW_TTkwPkc', '2');
//
//        $redis->hSet('gameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '2', 1);
//        $redis->hSet('gameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '7', 1);
//        $redis->sAdd('setGameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '7');
//        $redis->sAdd('setGameData:' . 'owEKj5R9O19xhICch0p7qQmed3Tc', '2');
//
//        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '2', 1);
//        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '7', 1);
//        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '4', 1);
//        $redis->hSet('gameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '8', 1);
//        $redis->sAdd('setGameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '4');
//        $redis->sAdd('setGameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '2');
//        $redis->sAdd('setGameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '7');
//        $redis->sAdd('setGameData:' . 'owEKj5b23gZVTpwj5B0HSFXPtg7A', '8');




    }

    public function studyShow(Request $request) {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }
        $data = $request->all();
        $openid = $data['id'];
        $subjects = $redis->hGetAll("subjectData:$openid");
        arsort($subjects);
        array_splice($subjects, 3);
        $subjects = array_keys($subjects);
        $result = StudyInformation::query()->whereIn('subject', $subjects)->get()->toArray();
        return msg(0, $result);
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
            $id1_s = $redis->hget( "gameData:$firstOpenid", $data[$i]);
            if (!$id1_s) {
                $id1_s = 0;
            }
            $id2_S = $redis->hget( "gameData:$secondOpenid", $data[$i]);
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
     * game
     * @param Request $request
     * @return false|string
     */
    public function gameShow(Request $request)
    {
        try {
            $redis = new Redis();
            $redis->connect("game_redis", 6379);
        } catch (Exception $e) {
            return msg(500, "连接redis失败" . __LINE__);
        }
        $data = $request->all();
        $masterId = $data['id'];
        $data = array();
        for( $userId = 1; $userId <= User::query()->count(); $userId++)
        {
            $check = User::query()->find($userId);
            if (!$check){
                continue;
            }
            $check = $check['openid'];
            if($check != $masterId)
            {
                $diff = $redis->sInter("setGameData:".$masterId,"setGameData:".$check);
                if (isset($diff[0])) {
                    print_r($diff);
                    $union = $redis->sunion("setGameData:".$masterId,"setGameData:".$check);
                    $data[$userId] = $this->show($union, $masterId, $check );
                }
            }
        }
        arsort($data);
        $data = array_keys($data);
        $res = User::query()->whereIn('id', $data)->get()->toArray();
        return msg(0, $res);
    }
}
