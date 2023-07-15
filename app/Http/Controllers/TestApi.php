<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestApi extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function __construct()
    {
        //middleware : anelan'elan'ny controller sy route,eto zao token ny middleware
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        //mandray an'ilay données(request)
        $nom_user = $request->input('nom_user');
        $mdp_user = $request->input('mdp_user');

        $user = User::where('nom_user', $nom_user)->first();

        if ($user && md5($mdp_user)==$user->mdp_user ) {
            $token = $this->guard()->login($user);
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }
}
