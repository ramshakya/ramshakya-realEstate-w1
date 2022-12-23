<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use phpseclib3\Crypt\Hash;

class checkUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization') != null) {
            return $next($request);
        } else {
            $response = [
                "errors" => "token invalid"
            ];
            return response($response, 401);
        }
    }
}
