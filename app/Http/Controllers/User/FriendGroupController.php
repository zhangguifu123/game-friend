<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Group;
use App\Models\User\GroupRelation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FriendGroupController extends Controller
{
    public function make(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 1);
        if (!is_array($data)) {
            return $data;
        }
        $groupData    = ['master_id' => $request->route('uid'), 'name' => $data['groupName']];
        $group   = new Group($groupData);
        $group->save();
        $masterId = $request->route('uid');
        $groupId = DB::table('group')->insertGetId($data);
        foreach ($data['friends'] as $value) {
            $groupRelation = new GroupRelation(['group_id' => $groupId, 'user_id' => $value, 'master_id' => $masterId]);
            $groupRelation->save();
        }
        return msg(0, __LINE__);
    }

    public function getJoinList(Request $request){
        if (!$request->route('uid')) {
            return msg(3 , __LINE__);
        }
        $group = GroupRelation::query()->where('user_id', $request->route('uid'))->get('group_id')->toArray();
        return msg(0, $group);
    }
    public function getMeList(Request $request){
        if (!$request->route('uid')) {
            return msg(3 , __LINE__);
        }
        $group = Group::query()->where('user_id', $request->route('uid'))->get('id')->toArray();
        return msg(0, $group);
    }
    //添加群成员
    public function add(Request $request){

        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 2);
        if (!is_array($data)) {
            return $data;
        }
        $groupId = Group::query()->find($data["group_id"])->id;
        if (!$groupId) {
            return msg(3, "目标不存在" . __LINE__);
        }
        $masterId = $request->route('uid');
        $groupRelation = new GroupRelation(['group_id' => $groupId, 'user_id' => $data['friend'], 'master_id' => $masterId]);
        $groupRelation->save();
        return msg(0, __LINE__);
    }
    //删除群聊
    public function deleteGroup(Request $request){
        //通过路由获取前端数据，并判断数据格式
        if (!$request->input('groupId')){
            return msg(11, __LINE__);
        }
        $group = Group::query()->find($request->input('groupId'));
        if (!$group) {
            return msg(11, __LINE__);
        }
        $group->delete();
        return msg(0, __LINE__);
    }
    //移除群成员
    public function delete(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 2);
        if (!is_array($data)) {
            return $data;
        }
        $group = Group::query()->find($data["group_id"]);
        if (!$group) {
            return msg(3, "目标不存在" . __LINE__);
        }
        $groupRelation = GroupRelation::query()->where('user_id', $data['friend']);
        if (!$groupRelation) {
            return msg(11, __LINE__);
        }
        $groupRelation->delete();
        return msg(0, __LINE__);
    }
    //检查函数
    private function _dataHandle(Request $request = null, $num){
        //声明理想数据格式
        if ($num == 1) {
            $mod = [
                "friends"    => ["array"],
                "groupName"  => ["string"],
            ];
        } else {
            $mod = [
                "friend"     => ["string"],
                "group_id"   => ["string"],
            ];
        }

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
