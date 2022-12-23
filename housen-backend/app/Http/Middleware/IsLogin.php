<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsLogin
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
        $authValue = auth()->user();
        if (is_null($authValue)) {
            return redirect('agent/')->with('',"Please Login.");
        }
        if(auth()->user()->person_id == 3) {
            return $next($request);
        }
        if(auth()->user()->person_id == 2) {
            return $next($request);
        }
        if(auth()->user()->person_id == 1) {
            return $next($request);
        }
        return redirect('agent/')->with('',"Please Login.");
    }
}
