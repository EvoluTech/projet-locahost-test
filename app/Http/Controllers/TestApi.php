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
        //middleware = polisy misava ticket
    }
    function convStringTimestamp($dateString) {
        $date = Carbon::createFromFormat('d/m/Y', $dateString);
        return $date;
    }

    public function login(Request $request)
    {
        //request eto dia mandray an le post fa mbola tsy mverifier

        //mandray an'ilay données(request)
        $nom_user = $request->input('nom_user');
        $mdp_user = $request->input('mdp_user');

        $user = User::where('nom_user', $nom_user)->first();

        if ($user && md5($mdp_user)==$user->mdp_user )
        {
            $token = $this->guard()->login($user);
            return response()->json(
                [
                    'token' => $token
                ]
                ,
                 200
                );
        }

        return response()->json
        (
            [
                'error' => 'Unauthorized'
            ]
            , 401
        );
    }

   /* protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }*/




    public function creerCompte(Request $request) {
        $validator = Validator::make($request->all(), [
                'id_user'=> 'required|integer',
                'nom_user' => 'required|string|max:255',
                'prenom_user' => 'required|string|max:255',
                'mdp_user' => 'required|string|min:8',
                'type_user' => 'required|string',
                'adresse_user' => 'required|string|max:255',
                'adresse_mail' => 'required|email|unique:users',
            ], [
                'required' => 'Le champ :attribute est obligatoire.',
                'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
                'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
                'email' => 'L\'adresse email saisie n\'est pas valide.',
                'unique' => 'L\'adresse email est déjà utilisée par un autre utilisateur.',
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


        $id_user = $request->input('id_user');
        $nom_user = $request->input('nom_user');
        $prenom_user = $request->input('prenom_user');
        $mdp_user = $request->input('mdp_user');
        $type_user = $request->input('type_user');
        $adress_user = $request->input('adress_user');
        $adresse_mail = $request->input('adresse_mail');


        try
        {
            $insertion=DB::insert(
                "INSERT INTO public.users(
                    id_user,
                    nom_user,
                    prenom_user,
                    mdp_user,
                    type_user,
                    adresse_user,
                    adresse_mail)
                VALUES (
                    '".$id_user."',
                    '".$nom_user."',
                    '".$prenom_user."',
                    '".$mdp_user."',
                    '".$type_user."',
                    '".$adress_user."',
                    '".$adresse_mail."')
                ");

                return  response()->json
                (
                    [
                        "status" => true,
                        "message"=> "Insertion réussit"
                    ], 200
                );

        } catch (\Throwable $th)
        {
            return  response()->json
            (
                [
                    "status" => false,
                    "erreur" => $th,
                    "message"=> "Insertion non réussit"
                ], 400
            );
        }
    }






    public function destroy($id)
    {
        $verfie=DB::select
        (
            'select *
            from users
            where id_user = ? ', [$id]
        );

        if (empty($verfie)) {
            return response()->json
                (
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

            return response()->json
                (
                    [
                        'status'=>true,
                        'message'=>"Suppression reuissie !"
                    ], 200
                );
        }
    }






    public function update(Request $request, $id)
    {
        $verfie=DB::select
            (
                'select *
                from users
                where id_user = ? ', [$id]
            );

        if (empty($verfie))
        {
            return response()->json
            (
                [
                    'status'=>false,
                    'message'=>"ID n'existe pas"
                ], 400
            );
        }
        else
            {
                $validator = Validator::make($request->all(), [
                    'nom_user' => 'required|string|max:255',
                    'prenom_user' => 'required|string|max:255',
                    'mdp_user' => 'required|string|min:8',
                    'type_user' => 'required|string',
                    'adresse_user' => 'required|string|max:255',
                    'adresse_mail' => 'required|email',
                ], [
                    'required' => 'Le champ :attribute est obligatoire.',
                    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                    'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
                    'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
                    'email' => 'L\'adresse email saisie n\'est pas valide.',
                    'unique' => 'L\'adresse email est déjà utilisée par un autre utilisateur.',
                ]);

                if ($validator->fails())
                {
                    // Renvoyer une réponse JSON avec les erreurs de validation
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $nom_user = $request->input('nom_user');
                $prenom_user = $request->input('prenom_user');
                $mdp_user = $request->input('mdp_user');
                $type_user = $request->input('type_user');
                $adresse_user = $request->input('adresse_user');
                $adresse_mail = $request->input('adresse_mail');


                try
                {
                    $update = DB::update(
                        '
                        UPDATE users
                        SET nom_user = ?,
                        prenom_user = ?,
                        mdp_user = ?,
                        type_user = ?,
                        adresse_user = ?,
                        adresse_mail = ?

                        WHERE id_user = ?',
                            [
                                $nom_user,
                                $prenom_user,
                                $mdp_user,
                                $type_user,
                                $adresse_user,
                                $adresse_mail,
                                $id]);

                       return  response()->json
                       (
                           [
                               "status" => true,
                               "message"=> "Mis à jour réussit"
                           ], 200
                       );

                } catch (\Throwable $th)
                    {
                        return  response()->json
                        (
                            [
                                "status" => false,
                                "erreur" => $th,
                                "message"=> "Mis à jour non réussit"
                            ], 400
                        );
                    }
            }
    }
    public function select()
    {
       $recherche = DB::select(
        'SELECT *
        FROM users'

        );

         if (empty($recherche))
        {
            // Gérer le cas où aucun utilisateur n'est trouvé pour l'ID spécifié
            return response()->json
            (
                [
                    "status" => false,
                    'message' => 'Aucun bien trouvé '
                ], 404
            );
        }

        // Renvoyer la liste des utilisateurs sous forme de réponse JSON
        return response()->json
        (
            [
                "status" => true,
                'data' => $recherche,
                'message' => 'voici la liste des biens trouvés '

            ], 200
        );
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
        $date_postuler = $this->convStringTimestamp($request->input('date_postuler'));


        $prix_biens = $request->input('prix_biens');
        $prix_par_jour = $request->input('prix_par_jour');
        $prix_total_payer = $request->input('prix_total_payer');
        $etat_biens = $request->input('etat_biens');
        $description_biens = $request->input('description_biens');
        $ville = $request->input('ville');
        $id_objet = $request->input('id_objet');
        $type_annee = $request->input('type_annee');
        $id_user = $request->input('id_user');

        $result = DB::select("SELECT nextval('id_detail_bien_postuler') as next_value");
        $sequence_id_detail_bien = $result[0]->next_value;



        $res1 = DB::select("SELECT nextval('id_bien_postuler') as next_value");
        $sequennce_id_bien_postuler = $res1[0]->next_value;


        $nombre_vue_detailsbienspostuler = 0;
        $signalisation_detailsbienspostuler = 0;
        $status_detailsbienspostuler = 0;

        $dataDetailsB=[$sequence_id_detail_bien,$nombre_vue_detailsbienspostuler,$signalisation_detailsbienspostuler,$status_detailsbienspostuler];
        $sqlInsert="INSERT INTO public.detailsbienspostuler(id_detailsbienspostuler,nombre_vue_detailsbienspostuler,signalisation_detailsbienspostuler,status_detailsbienspostuler)
        values (?,?,?,?)";

        $dataPostuler=[$id_user, $sequennce_id_bien_postuler, $date_postuler];
        $sqlInsert1="INSERT INTO public.postuler(id_user, id_bienspostuler, date_postuler)
        values(?,?,?)";

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
                    '$date_debut_postule', 
                    '$date_fin_postule',   
                    '$prix_biens',         
                    '$prix_par_jour',      
                    '$prix_total_payer',   
                    '$etat_biens',         
                    '$description_biens',  
                    '$ville',              
                    '$id_objet' ,           
                    '$sequence_id_detail_bien',
                    '$type_annee',
                    :photos_1,
                    :photos_2,
                    :photos_3,
                    :photos_4
                );"
                ,$imagePaths
            );

            $inserta = DB::insert($sqlInsert1,$dataPostuler);

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

public function search($id_user,$id_objet)
    {
       $search = DB::select(
        'SELECT u.pdp as sarytapaka, u.nom_user as nom,
                bp.photos_1 as sary1, bp.photos_2 as sary2, bp.photos_3 as sary3, bp.adresse_bien as descritionlieu,
                b.type_objet as typebien,
                bp.description_biens as optione, bp.prix_biens as prisis, bp.type_annee as mois, bp.pub_bien as détail
        FROM users u, bienspostuler bp, biens b,postuler ps
        WHERE u.id_user=ps.id_user and ps.id_bienspostuler=bp.id_bienspostuler and u.id_user=? and b.id_objet=?',[$id_user,$id_objet]);

         if (empty($search))
        {
            // Gérer le cas où aucun utilisateur n'est trouvé pour l'ID spécifié
            return response()->json
            (
                [
                    "status" => false,
                    'message' => 'Aucun bien trouvé '
                ], 404
            );
        }

        // Renvoyer la liste des utilisateurs sous forme de réponse JSON
        return response()->json
        (
            [
                "status" => true,
                'data' => $search,
                'message' => 'voici la liste des biens trouvés '

            ], 200
        );
    }

public function searchpub($id_user)
    {

    $publications = DB::select(
        'SELECT u.pdp as sarytapaka, u.nom_user as nom,
                bp.photos_1 as sary1, bp.photos_2 as sary2, bp.photos_3 as sary3, bp.adresse_bien as descritionlieu,
                b.type_objet as typebien,
                bp.description_biens as optione, bp.prix_biens as prisis, bp.type_annee as mois, bp.pub_bien as détail
        FROM users u,postuler ps, bienspostuler bp, biens b
        WHERE u.id_user=ps.id_user and ps.id_bienspostuler=bp.id_bienspostuler and u.id_user=?;',[$id_user]);

    if (empty($publications)) {
        return response()->json([
            "status" => false,
            'message' => 'Aucune publication trouvée'
        ], 404);
    }

    return response()->json([
        "status" => true,
        'data' => $publications,
        'message' => 'Voici la liste des publications de l\'utilisateur'
    ], 200);
}

public function listeBiens($id_objet)
    {

    $publications = DB::select(
        'SELECT bp.photos_1 as sary1, bp.photos_2 as sary2, bp.photos_3 as sary3, bp.adresse_bien as descritionlieu,
                b.type_objet as typebien,
                bp.description_biens as optione, bp.prix_biens as prisis, bp.pub_bien as détail
        FROM bienspostuler bp, biens b
        WHERE b.type_objet=b.type_objet and b.id_objet=?;',[$id_objet]);

    if (empty($publications)) {
        return response()->json([
            "status" => false,
            'message' => 'Aucune publication trouvée'
        ], 404);
    }

    return response()->json([
        "status" => true,
        'data' => $publications,
        'message' => 'Voici la liste des biens'
    ], 200);
}


}





