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
        //middleware = polisy misava ticket
    }

    public function login(Request $request)
    {
        //request eto dia mandray an le post fa mbola tsy mverifier

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

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }




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



}
