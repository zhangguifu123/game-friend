<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\User\Post;
use App\Models\Manager\Game\Comment;
use App\Models\Manager\Game\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    //评测评论
    public function publish(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->data_handle($request);
        if (!is_array($data)) {
            return $data;
        }
        $postId = $request->route('postId');
        $data = $data + ["post_id"=>$postId,"status" => 0];
        $comments = new Comment($data);

        if ($comments->save()) {
            return msg(0,$comments->id);
        }
        //未知错误
        return msg(4, __LINE__);
    }

    //获取征友贴评论
    public function getList(Request $request){
        $all_list = [];
        $comment_list = Comment::query()->
        where('post_id','=',$request->route('postId'))
            ->leftJoin('users','comments.fromId','=','users.id')
            ->get([
                'comments.id','toId','fromId','users.name as fromName','users.avatar as fromAvatar','content','comments.created_at as time'
            ])->toArray();
        foreach ($comment_list as $i){
            $reply_list = Reply::query()
                ->where('comment_id','=',$i['id'])
                ->leftJoin('users','replies.fromId','=','users.id')
                ->get([
                    'replies.id','fromId','users.name as fromName','toId','comment_id','users.avatar as fromAvatar','content','replies.created_at as time'
                ])->toArray();
            $all_list[] = $i + ['reply'=>$reply_list];
        }

        $message = ['total' => count($comment_list), 'list' => $all_list];
        return msg(0, $message);
    }

    //获取指定用户评论
    public function getOneList(Request $request){
        //提取数据
        $uid = $request->route('uid');
        $page = $request->route('page');
        $limit = 13;
        $offset = $page * $limit - $limit;
        //查看评论
        $comment = Comment::query()
            ->where([
                ['comments.fromId', $uid],
            ])
            ->leftJoin('users', 'comments.fromId', '=', 'users.id')
            ->whereIn('comments.handleStatus', [0, 1]);
        $listSum = $comment->count();
        $list = $comment
            ->limit(13)
            ->offset($offset)
            ->orderByDesc('comments.created_at')
            ->get(['comments.id', 'comments.post_id', 'comments.toId', 'comments.fromId', 'comments.status', 'users.name as fromName', 'users.avatar as fromAvatar', 'comments.content', 'comments.like', 'comments.handleStatus', 'comments.created_at', 'comments.updated_at']);
        if(!$list){
            return msg(4,__LINE__);
        }
        $message = ['total' => $listSum, 'limit' => $limit, 'list' => $list];
        return msg(0,$message);
    }


    //删除
    public function delete(Request $request)
    {

        $comments = Comment::query()->find($request->route('id'));
        // 将该评测从我的发布中删除

        $toId = $comments->id;
        $reply = Reply::query()->find($toId);
        if ($reply){
            $reply->delete();
        }
        $comments->delete();
        $data = ['删除comment_id' => $toId];
        return msg(0, $data);
    }

    //检查函数
    private function data_handle(Request $request = null){
        //声明理想数据格式
        $mod = [
            "fromId" => ["string"],
            "fromName" => ["string"],
            "fromAvatar" => ["string"],
            "content" => ["string", "max:50"]
        ];
        //是否缺失参数
        if (!$request->has(array_keys($mod))){
            return msg(1,__LINE__);
        }
        //提取数据
        $data = $request->only(array_keys($mod));
        //判断是否存在昵称，没有获取真实姓名并加入
        if ($data["fromName"] === ""||empty($data["fromName"])){
            if ($request->routeIs("Comment_update")) {
                $uid = Comment::query()->find($request->route('id'))->fromId;
            }
            $data["name"] = User::query()->find($uid)->name;
        }
        //判断数据格式
        if (Validator::make($data, $mod)->fails()) {
            return msg(3, '数据格式错误' . __LINE__);
        };
        //查找征友贴发布者id
        $toId = Post::query()->find($request->route('postId'))->publisher;
        $data = $data + ["toId"=>$toId];
        return $data;
    }
}
