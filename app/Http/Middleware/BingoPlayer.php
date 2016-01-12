<?php

namespace App\Http\Middleware;

use Closure;

class BingoPlayer
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
        if(!$request->user()->isBingoPlayer()){
            return redirect('attendance');
        }
        return $next($request);
    }
}
