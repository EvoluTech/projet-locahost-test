<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyToken
{
  protected $expect = [
    'login'
  ];
    public function handle(Request $request, Closure $next)
    {
        //refa login de mi passe
        if($this->shouldExcludeRoute($request)){
            return $next($request);
        }
        //verification token
        try {
            $user = Auth::guard('api')->user();

            if(!user){
                throw new AuthorizationException('Unauthrized!!');
            }
            return $next($request);
        }
         catch (AuthorizationException $e) {
            return response()->json(['error'=> $e->getMessage()], 401);
        }
        
    }
    protected function shouldExcludeRoute($request){
        return collect($this->expect)->contains (function($route) use ($request){
            return $request->is($route);
        });
        
    }
}

