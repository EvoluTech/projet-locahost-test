<?php

namespace App\Http\Middleware;

use Closure;
<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
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

            if(!$user){
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

=======
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected $except = [
        'login',
    ];

    public function handle(Request $request, Closure $next)
    {
        //refa login le route de mi-passe tsa mila token
        if ($this->shouldExcludeRoute($request)) {
            return $next($request);
        }

        //mila vérification token
        try {
            $user = Auth::guard('api')->user();

            if (!$user){
                throw new AuthorizationException('Unauthorized__');
            }

            return $next($request);
            
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()],401);
        }

    }

    protected function shouldExcludeRoute($request)
    {
        return collect($this->except)->contains(function ($route) use ($request){
            return $request->is($route);
        });
    }
}
>>>>>>> origin/Oly
