<?php

namespace App\Http\Middleware;

use Closure;

class RoleCheck
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
        if(!$request->user()->isAdmin()){
            if($request->user()->role()->first()->role == 'bp'){
                return redirect('bingo');
            }
            else{
                return redirect('attendance/list');
            }
        }
        return $next($request);
    }
}
