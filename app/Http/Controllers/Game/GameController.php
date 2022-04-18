<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Manager\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    //评测评论
    public function publish(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->data_handle($request);
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

    //检查函数
    private function data_handle(Request $request = null){
        //声明理想数据格式
        $mod = [
            "img"            => ["json"],
            "publisher"      => ["string"],
            "name"           => ["string", "max:50"],
            "sign_up_time"   => ["string"],
            "game_time"      => ["string", "nullable"],
            "organizer"      => ["string"],
            "subject"        => ["string", "nullable"],
            "type"           => ["string", "nullable"],
            "level"          => ["string"],
        ];
        //是否缺失参数
        if (!$request->has(array_keys($mod))){
            return msg(1,__LINE__);
        }
        //提取数据
        $data = $request->only(array_keys($mod));
        //判断是否存在昵称，没有获取真实姓名并加入
        if ($data["nickname"] === ""||empty($data["nickname"])){
            if ($request->routeIs("evaluation_update")) {
                $uid = Game::query()->find($request->route('id'))->publisher;
            }
            $data["nickname"] = User::query()->find($uid)->nickname;
        }
        //判断数据格式
        if (Validator::make($data, $mod)->fails()) {
            return msg(3, '数据格式错误' . __LINE__);
        };
        return $data;
    }

}
