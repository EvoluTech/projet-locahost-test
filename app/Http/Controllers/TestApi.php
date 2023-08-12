<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

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
    function convStringTimestamp($dateString) {
        $date = Carbon::createFromFormat('d/m/Y', $dateString);
        return $date;
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

    public function getNextSequenceValue()
{
    // Exécutez la requête pour récupérer la prochaine valeur de la séquence
    $result = DB::select("SELECT nextval('id_detail_bien_postuler') as next_value");

    // Renvoyez la valeur générée en tant que réponse JSON
    return response()->json(['next_value' => $result[0]->next_value], 200);
}



public function BiensPostuler(Request $request)
{
    $validator = Validator::make($request->all(), [

            'date_debut_postule' => 'required',
            'date_fin_postule' => 'required',
            'prix_biens' => 'required',
            'prix_par_jour' => 'required',
            'prix_total_payer' => 'required',
            'etat_biens' => 'required',
            'description_biens' => 'required',
            'ville' => 'required',
            'id_objet' => 'required',
            'type_annee' => 'required',

        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'string' => 'Le champ :attribute doit être une chaîne de caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ]);

        if ($validator->fails())
        {
            // Renvoyer une réponse JSON avec les erreurs de validation
            return response()->json
            (
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }


        $date_debut_postule = $this->convStringTimestamp($request->input('date_debut_postule'));
        $date_fin_postule = $this->convStringTimestamp($request->input('date_fin_postule'));
        $prix_biens = $request->input('prix_biens');
        $prix_par_jour = $request->input('prix_par_jour');
        $prix_total_payer = $request->input('prix_total_payer');
        $etat_biens = $request->input('etat_biens');
        $description_biens = $request->input('description_biens');
        $ville = $request->input('ville');
        $id_objet = $request->input('id_objet');
        $type_annee = $request->input('type_annee');
        $result = DB::select("SELECT nextval('id_detail_bien_postuler') as next_value");
        $sequence_id_detail_bien = $result[0]->next_value;

        $nombre_vue_detailsbienspostuler = 0;
        $signalisation_detailsbienspostuler = 0;
        $status_detailsbienspostuler = 0;

        $dataDetailsB=[$sequence_id_detail_bien,$nombre_vue_detailsbienspostuler,$signalisation_detailsbienspostuler,$status_detailsbienspostuler];
        $sqlInsert="INSERT INTO public.detailsbienspostuler(id_detailsbienspostuler,nombre_vue_detailsbienspostuler,signalisation_detailsbienspostuler,status_detailsbienspostuler)
        values (?,?,?,?)";
        try {
            $insert = DB::insert($sqlInsert,$dataDetailsB);

            $imagePaths = [];
            for ($i = 1; $i <= 4; $i++) {
                $currentTimestamp = Carbon::now()->timestamp."".$i;
                if ($request->hasFile("photos_$i")) {
                    $image = $request->file("photos_$i");
                    $imageName = $currentTimestamp.'_'.$image->getClientOriginalName();
                    $imagePath = $image->storeAs('uploads', $imageName, 'public');
                    $imagePaths["photos_$i"] = $imagePath;
                }
            }

            $insertion = DB::insert(
                "INSERT INTO public.bienspostuler(
                    id_bienspostuler,
                    date_debut_postule,
                    date_fin_postule,
                    prix_biens,
                    prix_par_jour,
                    prix_total_payer,
                    etat_biens,
                    description_biens,
                    ville,
                    id_objet,
                    id_detailsbienspostuler,
                    type_annee,
                    photos_1,
                    photos_2,
                    photos_3,
                    photos_4)
                VALUES (
                    nextval('id_bien_postuler'),
                    '$date_debut_postule', -- Utilisation de la variable
                    '$date_fin_postule',   -- Utilisation de la variable
                    '$prix_biens',         -- Utilisation de la variable
                    '$prix_par_jour',      -- Utilisation de la variable
                    '$prix_total_payer',   -- Utilisation de la variable
                    '$etat_biens',         -- Utilisation de la variable
                    '$description_biens',  -- Utilisation de la variable
                    '$ville',              -- Utilisation de la variable
                    '$id_objet' ,           -- Utilisation de la variable
                    '$sequence_id_detail_bien',-- Utilisation de la variable
                    '$type_annee',
                    :photos_1,
                    :photos_2,
                    :photos_3,
                    :photos_4
                );" 
                ,$imagePaths
            );

            return response()->json(
                [
                    "status" => true,
                    "message" => "Insertion réussie"
                ], 200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "status" => false,
                    "erreur" => $th,
                    "message" => "Insertion non réussie"
                ], 400
            );
        }
}





}

        
         
    
    

