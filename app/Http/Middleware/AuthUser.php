<?php

namespace App\Http\Middleware;

use App\User;
use App\Helpers\JwtAuthHelper;

use Closure;

class AuthUser
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
        $jwtAuth = new JwtAuthHelper();
        $jwt = request()->header('Authorization');

        if(  !$jwt || !$jwtAuth->checkToken($jwt) ){

            return response()->json(['message'=>'Token errado ou expirado', 'status'=>'error'],200);
            
        }else{
            return $next($request);
        }
        
    }
}
