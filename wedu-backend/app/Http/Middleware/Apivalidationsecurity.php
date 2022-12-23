<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Apivalidationsecurity
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
        // if($request->getHost() != env('FRONTENDSECURITYURL'))
        // {
            $url = request()->headers->all();
            /*if ((!isset($url['origin']))|| (!isset($url['origin']['0']) && ($url['origin']['0'] != env('FRONTENDSECURITYURL') || $url['origin']['0'] != 'https://panel.wedu.ca'))) {
                $responseArray['error'] = "You are not authorized to access this request";
                $responseArray['status'] = 401;
                return response()->json($responseArray, 401);
            }*/
        // }
        
        return $next($request);
    }
}
