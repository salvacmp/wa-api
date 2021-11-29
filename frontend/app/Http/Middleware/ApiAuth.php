<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = ApiKey::find(1)->api_key;
        if($request->bearerToken() === $key){
            return $next($request);
        }else{
            return response()->json(["status" => 403, "message"=>"Unauthorized"],403);
        }

    }
}
