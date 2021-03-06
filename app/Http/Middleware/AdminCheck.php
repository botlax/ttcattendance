<?php

namespace App\Http\Middleware;

use Closure;

class AdminCheck
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
        if(!$request->user()->isNotAdmin()){
             if($request->user()->role()->first()->role == 'bp'){
                return redirect('bingo');
            }
            else{
                return redirect('attendance');
            }
        }

        return $next($request);
    }
}
