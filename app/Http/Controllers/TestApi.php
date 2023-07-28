<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class TestApi extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $nom_user = $request->input('nom_user');
        $mdp_user = $request->input('mdp_user');

        $user = User::where('nom_user', $nom_user)->first();

        if ($user && md5($mdp_user)==$user->mdp_user ) {

            $token = $this->guard()->login($user);
            return response()->json(
                [
                    'token' => $token,
                    'message' => 'Bienvenue !',
                    'status' => true,
                    'user'=>$user,
            ], 
            200);
            
        }
       
            return response()->json(
                [
                    // 'error' => 'Unauthorized',
                    'message' => 'Votre Mot de passe est incorrect',
                    'status' => false,
                ]
                ,
                401);
    }

    public function afficheTest()
    {
        $users = User::all();
        return response()->json(["data"=>$users,"status"=>true,"Nombre Utilisateur"=>$this->getLastUserId()]);
    }

    public function getLastUserId()
    {
        $lastUserId = User::orderBy('id_user', 'desc')->value('id_user');
        return $lastUserId;
    }
    public function  creerCompte (Request $request){
        $id_user=$this->getLastUserId()+1;
        $nom_user=$request->input('nom_user');
        $prenom_user=$request->input('prenom_user');
        $mdp_user=$request->input('mdp_user');
        $type_user=$request->input('type_user');
        $adresse_user=$request->input('adresse_user');
        $adresse_mail=$request->input('adresse_mail');
        $mdp_crypte=md5($mdp_user);

        $validator = Validator::make(
            [
                'id_user' => $id_user,
                'nom_user' => $nom_user,
                'prenom_user' => $prenom_user,
                'mdp_user' => $mdp_user,
                'type_user' => $type_user,
                'adresse_user' => $adresse_user,
                'adresse_mail' => $adresse_mail
            ],
            [
                'id_user' => 'required|integer|max:50',
                'nom_user' => 'required|string',
                'prenom_user' => 'required|string|max:50',
                'mdp_user' => 'required|max:50',
                'type_user' => 'required|string|max:50',
                'adresse_user' => 'required|string|max:50',
                'adresse_mail' => 'required|email|max:50'
            ]
        );

        if ($validator->fails()) {
            // return $validator->errors();
            return response()->json($validator->errors(), 403);
        }
    
    
        try {
            DB::table('users')->insert([
                'id_user' => $id_user,
                'nom_user' => $nom_user,
                'prenom_user' => $prenom_user,
                'mdp_user' => $mdp_crypte,
                'type_user' => $type_user,
                'adresse_user' => $adresse_user,
                'adresse_mail' => $adresse_mail
            ]);
    
            return  response()->json(['status'=>true,'Message'=>'Insertion reuissite !'], 200); 
        } catch (\Exception $e) {

            return  response()->json(['status'=>false,'error'=>$e->getMessage(),'Message'=>'Erreur lors de ajout de utilisateur'], 401); 
        }
    }
  
}
