<?php

namespace App\Http\Middleware;

use App\Common\ErrorCode;
use Closure;
use Illuminate\Http\JsonResponse;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty($request->input('token'))){
            return new JsonResponse(['status'=>0,'code'=>ErrorCode::$token_missing['code'],'message'=>ErrorCode::$token_missing['message']]);
        }
        return $next($request);
    }
}
