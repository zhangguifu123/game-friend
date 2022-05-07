<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GameCollection;
use App\Models\PostCollection;
use App\Models\User\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //
    //发布赛事
    public function publish(Request $request){
        //通过路由获取前端数据，并判断数据格式
        $data = $this->_dataHandle($request);
        if (!is_array($data)) {
            return $data;
        }
        $post = new Post($data);

        if ($post->save()) {
            return msg(0,$post->id);
        }
        //未知错误
        return msg(4, __LINE__);
    }
    /** 拉取列表信息 */
    public function getList(Request $request)
    {
        if (!$request->input('uid')){
            return msg(11, __LINE__);
        }
        $uid = $request->input('uid');
        //分页，每页10条
        $limit = 10;
        $offset = $request->route("page") * $limit - $limit;
        $post = post::query();
        $postSum = $post->count();
        $postList = $post
            ->limit(10)
            ->offset($offset)->orderByDesc("posts.created_at")
            ->leftJoin('users','posts.publisher','=','users.openid')
            ->get([
                "posts.id", "publisher", "users.avatar", "posts.name", "level", "theme", "title" ,"content","img", "views", "posts.created_at"
            ])
            ->toArray();
        $postList = $this->_isCollection($uid, $postList);
        $message['postList'] = $postList;
        $message['total']    = $postSum;
        $message['limit']    = $limit;

        if (isset($message['token'])){
            return msg(13,$message);
        }
        return msg(0, $message);
    }
    private function _isCollection($uid, $postList){
        $postCollection = PostCollection::query()->where('uid', $uid)->get()->toArray();
        $collectionArray  = [];
        foreach ($postCollection as $value){
            $collectionArray[$value['post_id']] = $value['id'];
        }
        $newPostList = [];
        foreach ($postList as $post){
            if (array_key_exists($post['id'], $collectionArray)) {
                $post += ['isCollection' => 1, 'collectionId' => $collectionArray[$post['id']]];
            } else {
                $post += ['isCollection' => 0];
            };
            $newPostList[] = $post;
        }
        return $newPostList;
    }
    //判断近期是否浏览过该文章，若没有浏览量+1 and 建立近期已浏览session
    public function addView(Request $request){
        if (!$request->input('uid')){
            return msg(11, __LINE__);
        }
        $uid = $request->input('uid');
        $post = Post::query()->leftJoin('users','posts.publisher','=','users.openid')
            ->get([
                "posts.id", "publisher", "users.avatar", "posts.name", "level", "theme", "title" ,"content","img", "views", "posts.created_at"
            ])->find($request->route('id'));
        if (
            !session()->has("mark" . $request->route('id'))
            || session("mark" . $request->route('id')) + 1800 < time()
        ) {
            $post->increment("views");
            session(["mark" . $request->route('id') => time()]);
        }
        $postCollection = PostCollection::query()->where('uid', $uid)->get()->toArray();
        $collectionArray  = [];
        foreach ($postCollection as $value){
            $collectionArray[$value['post_id']] = $value['id'];
        }
        if (array_key_exists($post['id'], $collectionArray)) {
            $post->isCollection = 1;
            $post->collectionId = $collectionArray[$post['id']];
        } else {
            $post->isCollection = 0;
        };

        return msg(0,$post);
    }
    /** 删除 */
    public function delete(Request $request)
    {
        $post = Post::query()->find($request->route('id'));

        $imgs = Post::query()->find($request->route('id'))->img;
        $file = str_replace(config("app.url")."/storage/image/","",$imgs);
        $disk = Storage::disk('img');
        $disk->delete($file);
        $post->delete();

        return msg(0, __LINE__);
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
        $post = Post::query()->find($request->route('id'));
        $post = $post->update($data);
        if ($post) {
            return msg(0, __LINE__);
        }
        return msg(4, __LINE__);
    }
    //检查函数
    private function _dataHandle(Request $request){
        //声明理想数据格式
        $mod = [
            "img"          => ["string"],
            "publisher"    => ["string"],
            "theme"        => ["string"],
            "name"         => ["string", "max:50"],
            "title"        => ["string", "max:50"],
            "content"      => ["string"],
            "level"        => ["string"],
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
