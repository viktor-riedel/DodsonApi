<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsWebUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->is_api_user) {
            return $next($request);
        }
        abort(403);
    }
}
