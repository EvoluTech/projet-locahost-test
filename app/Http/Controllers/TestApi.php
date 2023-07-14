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
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $nom_utilisateur = $request->input('nom_utilisateur');
        $motpasse_utilisateur = $request->input('motpasse_utilisateur');

        $hashedPassword = Hash::make($motpasse_utilisateur);


        $user = User::where('nom_utilisateur', $nom_utilisateur)->first();

        if ($user ) {
            $token = $this->guard()->login($user);
            return response()->json(['token' => $token,'hase'=>$hashedPassword], 200);
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
