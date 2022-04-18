<?php

function msg($code, $msg) {
    $status = array(
        0 => '成功',
        1 => '缺失参数',
        2 => '账号密码错误',
        3 => '错误访问',
        4 => '未知错误',
        5 => '其他错误',
        6 => '未登录',
        7 => '重复访问',
        8 => '重复添加',
        9 => '无刷新次数',
        10 => '非本人',
        11 => '目标不存在',
        12 => '图片不和谐',
        13 => 'token已过期',
        14 => 'token未过期'
    );

    $result = array(
        'code' => $code,
        'status' => $status[$code],
        'data' => $msg
    );


    return json_encode($result, JSON_UNESCAPED_UNICODE);
}
