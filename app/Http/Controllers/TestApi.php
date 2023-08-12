<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
        //middleware = polisy misava ticket
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

        return response()->json
        (
            [
                'error' => 'Unauthorized'
            ]
            , 401
        );
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
            VALUES (?, ?, ?, ?, ?, ?, ?);",[$id_user,$nom_user,$prenom_user,$mdp_user,$type_user,$adresse_user,$adresse_mail]);

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

    public function modifierCompte($id_user,Request $request) {
        $nom_user=$request->input('nom_user');
        $prenom_user=$request->input('prenom_user');
        $mdp_user=$request->input('mdp_user');
        $type_user=$request->input('type_user');
        $adresse_user=$request->input('adresse_user');
        $adresse_mail=$request->input('adresse_mail');

        $modification=DB::update("UPDATE public.users 
        SET nom_user=?, prenom_user=?, mdp_user=?, type_user=?, adresse_user=?, adresse_mail=?
        WHERE id_user = ?",
        [$nom_user,$prenom_user,$mdp_user,$type_user,$adresse_user,$adresse_mail,$id_user]);

        if ($modification){
            return response()->json(
                [
                    'message' => 'Réussie',
                    'status' => true,
                    'erfgd' => $nom_user,
                ], 
                
            200);
        }

        return response()->json(['error' => 'Echec'], 401);

    }

    public function suprimer($id) {
        $verfie=DB::select
            (
                'select *
                from users
                where id_user = ? ', [$id]
            );
    
        if (empty($verfie)) {
    
            return response()->json (
                [
                    'status'=>false,
                    'message'=>"ID n'existe pas"
                ], 400
                );
        } else {
            $delete=DB::delete
            (
                'delete
                from users
                where id_user = ?', [$id]
            );
    
            return response()->json (
                [
                    'status'=>true,
                    'message'=>"Suppression réussie !"
                ], 200
                );
        }
    }

    public function uploadImage(Request $request)
{
    if ($request->hasFile('photo_1')) {
        $image = $request->file('photo_1');
        $imageName = $image->getClientOriginalName(); // Récupère le nom d'origine du fichier
        $imagePath = $image->storeAs('uploads', $imageName, 'public'); // Stocke l'image dans public/uploads

        // // Enregistrement du chemin de l'image dans la base de données
        // // Ici, nous utilisons DB::table() pour insérer les données
        $imageData = [
             'photos_1' => $imagePath,
             // Ajoutez d'autres colonnes si nécessaire
         ];
         DB::table('bienspostuler')->insert($imageData);

        return response()->json(['message' => 'OK']);
    }

    return response()->json(['error' => 'Aucune image téléchargée'], 400);
}

        
         
    
    }

