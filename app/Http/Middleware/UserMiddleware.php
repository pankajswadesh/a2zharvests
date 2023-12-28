<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class UserMiddleware
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
        
        if (isset(Auth::user()->id) && (Auth::user()->hasRole(['user']))) {
           
            return $next($request);
        }else{
            
            return redirect(route('home'));
        }
    }
}
