<?php

namespace App\Http\Middleware;

use Closure;

class SessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if($request->input('age') > $role){
            return redirect('home');
        }
        return $next($request);
    }

    public function terminate($request,$response){
        //do somebusiness
    }
}
