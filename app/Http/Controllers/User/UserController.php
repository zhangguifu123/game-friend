<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp;

class UserController extends Controller
{
    public function register(Request $request = null){
        $http = new GuzzleHttp\Client;
        $response = $http->get('https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
', [
            'form_params' => [
                'appid'      => 'wx434e0e175cbdd8a5',
                'secret'     => 'dc5793927faff4b09e60255fc206ea79-id',
                'grant_type' => 'authorization_code',
                'js_code'    => $request['js_code'],
            ],
        ]);
        $res    = json_decode( $response->getBody(), true);
        $result = [];
        $result['openid']      = $res['openid'];
        $result['session_key'] = $res['session_key'];

        $User = new User($result);
        $User->save();
        return msg(0, $res);
    }
    //
    //检查函数
    private function _dataHandle(Request $request = null){
        //声明理想数据格式
        $mod = [
            "group"    => ["string"],
            "user_id"  => ["string"],
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
