<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager\Game;
use App\Models\User;
use App\Models\User\Post;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    //
    public function statistics(Request $request) {
        $game = Game::all();
        $gameCount = $game->count();
        $gameCollectionCounts = $game->sum('collections');

        $userCount = User::all()->count();

        $post = Post::all();
        $postCount = $post->count();
        $postCollectionCount = $post->sum('collections');
        $postViewCounts = $post->sum('views');

        $count['game']['gameCount'] = $gameCount;
        $count['game']['gameCollectionCounts'] = $gameCollectionCounts;
        $count['user']['userCount'] = $userCount;
        $count['post']['postCount'] = $postCount;
        $count['post']['postCollectionCount'] = $postCollectionCount;
        $count['post']['postViewCounts'] = $postViewCounts;

        return msg(0, $count);
    }
}
