<?php
namespace GuojiangClub\Activity\Server\Http\Middleware;

use Closure;

class ActivityMiddleware
{

    public function handle($request, Closure $next)
    {
        app('app.discount')->setId(2);
        return $next($request);
    }

}