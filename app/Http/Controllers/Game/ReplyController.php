<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\Manager\Game\Comment;
use App\Models\Manager\Game\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplyController extends Controller
{
    //评论回复
    public function publish(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->data_handle($request);
        if (!is_array($data)) {
            return $data;
        }
        $comment = Comment::query()->find($request->route('commentId'));
        if(!$comment) {
            return response(msg(3, "评论不存在" . __LINE__));
        }
        $data = $data + ["status" => 0,"comment_id"=>$request->route("commentId")];
        $reply = new Reply($data);

        if ($reply->save()) {
            return msg(0, $reply->id);
        }
        //未知错误
        return msg(4, __LINE__);
    }

    public function getList(Request $request){
        $reply_list = Reply::query()
            ->where('replies.comment_id','=',$request->route('id'))
            ->leftJoin('users','replies.fromId','=','users.id')
            ->get([
                'replies.id','replies.fromId','users.name as fromName','replies.toId','replies.comment_id','users.avatar as fromAvatar','replies.content','replies.created_at as time'
            ])->toArray();
        $message = ['total' => count($reply_list), 'list' => $reply_list];
        return msg(0, $message);
    }


    //获取指定用户评论回复
    public function getOneList(Request $request){
        //提取数据
        $uid = $request->route('uid');
        $page = $request->route('page');
        $limit = 13;
        $offset = $page * $limit - $limit;
        //查看评论
        $reply = Reply::query()
            ->where([
                ['replies.fromId', $uid]
            ])
            ->whereIn('replies.handleStatus', [0,1])
            ->leftJoin('users','replies.fromId','=','users.id');
        $list = $reply
            ->limit(13)
            ->offset($offset)
            ->orderByDesc('replies.created_at')
            ->get(['replies.id', 'replies.comment_id', 'replies.fromId', 'users.name as fromName', 'replies.status', 'replies.toId', 'users.avatar as fromAvatar', 'replies.content', 'replies.handleStatus', 'replies.created_at', 'replies.updated_at']);
        if(!$list){
            return msg(4,__LINE__);
        }
        $message = ['total' => $reply->count(), 'limit' => $limit, 'list' => $list];
        return msg(0,$message);
    }

    //删除
    public function delete(Request $request)
    {
        $reply = Reply::query()->find($request->route('id'));
        // 将该评测从我的发布中删除
        $reply->delete();
        return msg(0, __LINE__);
    }

    //检查函数
    private function data_handle(Request $request = null){
        //声明理想数据格式
        $mod = [
            "toId" => ["string"],
            "fromId" => ["string"],
            "content" => ["string", "max:50"]
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
