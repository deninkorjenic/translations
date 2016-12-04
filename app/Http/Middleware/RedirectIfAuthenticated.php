<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        session_start();
        if(isset($_SESSION['current_user_id'])){
            if ($_SESSION['current_user_id']) {
            print_r($_SESSION['current_user_id']);
                return redirect('/home');
            }
        }
        
            return $next($request);
    }
}
