<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SocketAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user){
            if ($request->channel_name === 'App.Models.User.' . $user->id){
                return $next($request);
            }
        }

        return response()->json(["message" => "Unauthenticated."], 401);
    }
}
