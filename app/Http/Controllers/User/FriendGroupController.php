<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendGroupController extends Controller
{
    public function make(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 1);
        if (!is_array($data)) {
            return $data;
        }
        $group = new Group(['user_id' => $request->route('uid'), 'group' => $data['friends'], 'name' => $data['groupName']]);
        $group->save();
        return msg(0, __LINE__);
    }
    //添加群成员
    public function add(Request $request){

        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 2);
        if (!is_array($data)) {
            return $data;
        }
        $group = Group::query()->find($data["user_id"]);
        if (!$group) {
            return msg(3, "目标不存在" . __LINE__);
        }
        $groupList = json_decode($group['group'], true);
        if (!key_exists($data['friend'], $groupList)) {
            $groupList += $data['friend'];
        } else {
            return msg(4, __LINE__);
        }
        $group->group = json_encode($groupList);
        return msg(0, $groupList);
    }

    //移除群成员
    public function delete(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request, 2);
        if (!is_array($data)) {
            return $data;
        }
        $group = Group::query()->find($data["user_id"]);
        if (!$group) {
            return msg(3, "目标不存在" . __LINE__);
        }
        $groupList = json_decode($group['group'], true);
        if (key_exists($data['friend'], $groupList)) {
            $targetId = array_search($data['friend'], $groupList);
            unset($groupList[$targetId]);
        } else {
            return msg(4, __LINE__);
        }
        $group->group = json_encode($groupList);
        return msg(0, $groupList);
    }
    //检查函数
    private function _dataHandle(Request $request = null, $num){
        //声明理想数据格式
        if ($num == 1) {
            $mod = [
                "friends"    => ["json"],
                "groupName"  => ["string"],
            ];
        } else {
            $mod = [
                "user_id"    => ["string"],
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
