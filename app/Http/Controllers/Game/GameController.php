<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameCollection;
use App\Models\Manager\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    //发布赛事
    public function publish(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $game = new Game($data);

        if ($game->save()) {
            return msg(0,$game->id);
        }
        //未知错误
        return msg(4, __LINE__);
    }
    /** 拉取列表信息 */
    public function getList(Request $request)
    {
        if (!$request->input('uid') || is_null($request->input('subject')) || is_null($request->input('level'))){
            return msg(11, __LINE__);
        }
        $uid     = $request->input('uid');
        $level   = $request->input('level');
        $subject = $request->input('subject');
        //分页，每页10条
        $limit    = 10;
        $offset   = $request->route("page") * $limit - $limit;
        $game     = Game::query();
        $gameSum  = $game->count();
        $gameList = $game->limit(10)->offset($offset)->orderByDesc("games.created_at");
        if (!$level && !$subject) {
            $gameList = $gameList->get([
                "id", "publisher",  "name", "level", "subject" ,"sign_up_time",
                "content","game_time", "organizer", "collections", "img", "created_at"
            ])->toArray();
        }
        if ($level && $subject){
            $gameList = $gameList->where([
                ['level', 'in' , $level],
                ['subject', 'in' , $subject]
            ])->get([
                "id", "publisher",  "name", "level", "subject" ,"sign_up_time",
                "content","game_time", "organizer", "collections", "img", "created_at"
            ])->toArray();
        }
        if (!$level && $subject){
            $gameList = $gameList->where([
                ['subject', 'in' , $subject]
            ])->get([
                "id", "publisher",  "name", "level", "subject" ,"sign_up_time",
                "content","game_time", "organizer", "collections", "img", "created_at"
            ])->toArray();
        }
        if ($level && !$subject){
            $gameList = $gameList->whereIn('level', $level)->get([
                "id", "publisher",  "name", "level", "subject" ,"sign_up_time",
                "content","game_time", "organizer", "collections", "img", "created_at"
            ])->toArray();
        }
        $gameList = $this->_isCollection($uid, $gameList);
	    $message['gameList'] = $gameList;
        $message['total']    = $gameSum;
        $message['limit']    = $limit;
        if (isset($message['token'])){
            return msg(13,$message);
        }
        return msg(0, $message);
    }

    private function _isCollection($uid, $gameList){
        $gameCollection = GameCollection::query()->where('uid', $uid)->get()->toArray();
        $collectionArray  = [];
        foreach ($gameCollection as $value){
            $collectionArray[$value['game_id']] = $value['id'];
        }
        $newGameList = [];
        foreach ($gameList as $game){
            if (array_key_exists($game['id'], $collectionArray)) {
                $game += ['isCollection' => 1, 'collectionId' => $collectionArray[$game['id']]];
            } else {
                $game += ['isCollection' => 0];
            };
            $newGameList[] = $game;
        }
        return $newGameList;
    }
    /** 删除 */
    public function delete(Request $request)
    {
        $game = Game::query()->find($request->route('id'))->update(['status' => 0]);
        if ($game) {
            return msg(0, __LINE__);
        }
        return msg(4, __LINE__);
    }

    /** 修改 */
    public function update(Request $request)
    {
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        //如果$data非函数说明有错误，直接返回
        if (!is_array($data)) {
            return $data;
        }
        //修改
        $game = Game::query()->find($request->route('id'));
        $game = $game->update($data);
        if ($game) {
            return msg(0, __LINE__);
        }
        return msg(4, __LINE__);
    }

    //检查函数
    private function _dataHandle(Request $request = null){
        //声明理想数据格式
        $mod = [
            "img"            => ["json"],
            "publisher"      => ["string"],
            "name"           => ["string", "max:50"],
            "sign_up_time"   => ["string"],
            "content"        => ["string"],
            "game_time"      => ["string", "nullable"],
            "organizer"      => ["string"],
            "subject"        => ["string", "nullable"],
            "level"          => ["string"],
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
