<?php

namespace App\Http\Middleware\Auth;

use App\Models\Manager\Manager;
use Closure;
use Illuminate\Http\Request;

class ManagerCheck
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $model = new Manager();
        $isManager = $request->header('Authorization');
        $Authorization    = substr($isManager, 7);
        $level     = $model::query()->where('api_token', $Authorization)->first()->level;
        if ($level != 0) {
            return response(msg(10, __LINE__));
        }
        return $next($request);
    }
}
