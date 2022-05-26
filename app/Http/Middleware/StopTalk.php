<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class StopTalk
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $model = new User();
        $data = $request->input();
        if (isset($data['publisher'])) {
            $id = $data['publisher'];
        } else {
            $id = $data['fromId'];
        }

        $user  = $model::query()->where('openid', $id )->first();
        if (!$user) {
            return response(msg(11, __LINE__));
        }
        $level = $user['status'];
        if ($level == 1) {
            return response(msg(13, __LINE__));
        }
        return $next($request);
    }
}
