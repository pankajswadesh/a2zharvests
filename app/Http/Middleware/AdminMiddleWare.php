<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleWare
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
        if ( isset(Auth::user()->id) && (Auth::user()->hasRole(['admin','manager','supplier']))) {
            return $next($request);
        }else{
            return redirect(route('admin'));
        }
    }
}
