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

        if ($user && $mdp_user==$user->mdp_user) {

            $token = $this->guard()->login($user);
            return response()->json(
                [
                    'token' => $token,
                    'message' => 'Bienvenue',
                    'status' => true
                ], 
                
            200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function creerCompte(Request $request) {
        $id_user=$request->input('id_user');
        $nom_user=$request->input('nom_user');
        $prenom_user=$request->input('prenom_user');
        $mdp_user=$request->input('mdp_user');
        $type_user=$request->input('type_user');
        $adresse_user=$request->input('adresse_user');
        $adresse_mail=$request->input('adresse_mail');

        $insertion=DB::insert("INSERT INTO public.users(
            id_user, nom_user, prenom_user, mdp_user, type_user, adresse_user, adresse_mail)
            VALUES ('".$id_user."', '".$nom_user."', '".$prenom_user."', '".$mdp_user."', '".$type_user."', '".$adresse_user."', '".$adresse_mail."');");

            if ($insertion){
                return response()->json(
                    [
                        'message' => 'Réussie',
                        'status' => true
                    ], 
                    
                200);
            }

            return response()->json(['error' => 'Echec'], 401);
    }
}