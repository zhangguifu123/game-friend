<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp;

class UserController extends Controller
{
    public function login(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $http = new GuzzleHttp\Client;
        $response = $http->get('https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
', [
            'query' => [
                'appid'      => 'wx434e0e175cbdd8a5',
                'secret'     => 'dc5793927faff4b09e60255fc206ea79',
                'grant_type' => 'authorization_code',
                'js_code'    => $data['js_code'],
            ],
        ]);
        $res    = json_decode( $response->getBody(), true);
        if(!key_exists('openid',$res)){
            return msg(4, $res);
        }
        $data['openid'] = $res['openid'];
        $data['phone']  = '0';
        $check = DB::table('users')->where('openid', $res['openid'])->first();
        if (!$check){
            $User   = new User($data);
            $User->save();
            return msg(13, $User);
        }else{
            if ($check->phone == '0'){
                return msg(13, $check);
            }
            return msg(0, $check);
        }
    }
    public function check(Request $request){
        if (!$request->input('openid')) {
            return msg(1, __LINE__);
        }
        $openid = $request->input('openid');
        $check  = User::query()->where(
            'openid', $openid
        )->first();
        if ($check->phone == '0'){
            return msg(13, __LINE__);
        };
        return msg(0, __LINE__);

    }
    public function authenticate(Request $request){
        if (!$request->input('openid') || !$request->input('phone')) {
            return msg(1, __LINE__);
        }
        $phone  = $request->input('phone');
        $openid = $request->input('openid');
        $check  = User::query()->where(
            ['openid', $openid]
        )->first();
        if (!$check){
            return msg(11, __LINE__);
        };
        $check->phone = $phone;
        return msg(0, __LINE__);

    }
    //检查函数
    private function _dataHandle(Request $request){
        //声明理想数据格式
        $mod = [
            "name"    => ["string"],
            "js_code"  => ["string"],
            "avatar"  => ["string"],
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
