<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    //
    public function getMeList(Request $request){
        if (!$request->route('uid')) {
            return msg(3 , __LINE__);
        }
        $firstFriend = Friend::query()->where('user_id',   $request->route('uid'))->get('friend_id')->toArray();
        $lastFriend  = Friend::query()->where('friend_id', $request->route('uid'))->get('user_id')->toArray();
        $friendIds   = $firstFriend + $lastFriend;
        $friends = User::query()->whereIn('id', $friendIds)->get(['id','name','img'])->toArray();
        return msg(0, $friends);
    }
    public function getNotice(Request $request){
        if (!$request->route('uid')) {
            return msg(3 , __LINE__);
        }
        $Notices = Friend::query()->where('friend_id', $request->route('uid'))->where('status', 0)->get('user_id')->toArray();
        $friends = User::query()->whereIn('id', $Notices)->get(['id','name','avatar'])->toArray();
        return msg(0, $friends);
    }
    //确认申请
    public function updateStatus(Request $request){

        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }

        $friendRelation = Friend::query()->where([
            ['friend_id', $request->route('uid')],
            ['user_id', $data['friend']]
        ])->first();
        if (!$friendRelation){
            return msg(11 , __LINE__);
        }
        $friendRelation->update(['status' => 1]);
        return msg(0, __LINE__);
    }
    //发送好友申请
    public function add(Request $request){

        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $friendRelation = new Friend(['user_id' => $request->route('uid'), 'friend_id' => $data['friend']]);
        $friendRelation->save();
        return msg(0, __LINE__);
    }
    //移除群成员
    public function delete(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $friendRelation = Friend::query()->where([
            ['friend_id', $data['friend']],
            ['user_id', $request->route('uid')],
        ])->first();
        if (!$friendRelation) {
            return msg(11, __LINE__);
        }
        $friendRelation->delete();
        return msg(0, __LINE__);
    }
    //检查函数
    private function _dataHandle(Request $request = null){
        //声明理想数据格式
            $mod = [
                "friend"    => ["string"],
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
