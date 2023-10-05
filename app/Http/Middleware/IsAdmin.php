<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class IsAdmin
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
        if(Auth::check()){
        if (auth()->user()->email == 'dev@yopmail.com') {
        return $next($request);
        }
        else{
            Auth::logout();
            return redirect('login');
        }
        }
        else{
            Auth::logout();
             return redirect('login');
        }
    }
}
