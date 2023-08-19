<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;
use Illuminate\Support\Facades\Validator;

class Biens extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }



    public function creerBien(Request $request)
    {
        $validator = Validator::make($request->all(), [

                'nom_projet' => 'required',
                'type_objet' => 'required',
                'information_objet' => 'required',
                'description_objet' => 'required',
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


        //$id_objet = $request->input('id_objet');
        $nom_projet = $request->input('nom_projet');
        $type_objet = $request->input('type_objet');
        $information_objet = $request->input('information_objet');
        $description_objet = $request->input('description_objet');



        try
        {
            $insertion=DB::insert(
                "INSERT INTO public.biens(
                    id_objet,
                    nom_projet,
                    type_objet,
                    information_objet,
                    description_objet)

                VALUES (
                    nextval('id_bien'),
                    '".$nom_projet."',
                    '".$type_objet."',
                    '".$information_objet."',
                    '".$description_objet."');"
                );

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


    public function suprimer($id)
    {
        $verfie=DB::select
        (
            'select *
            from biens
            where id_objet = ? ', [$id]
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
                    from biens
                    where id_objet = ?', [$id]
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


    public function maj(Request $request, $id)
    {
        $verifie=DB::select
            (
                'select *
                from biens
                where id_objet = ? ', [$id]
            );

        if (empty($verifie))
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
                    'nom_projet' => 'required|string|max:255',
                    'type_objet' => 'required|string|max:255',
                    'information_objet' => 'required',
                    'description_objet' => 'required',

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

                $nom_projet = $request->input('nom_projet');
                $type_objet = $request->input('type_objet');
                $information_objet = $request->input('information_objet');
                $description_objet = $request->input('description_objet');

                try
                {
                    $update = DB::update(
                        'UPDATE biens
                        SET nom_projet =?,
                            type_objet =?,
                            information_objet =?,
                            description_objet =?
                        WHERE id_objet = ? ',
                            [
                                $nom_projet,
                                $type_objet,
                                $information_objet,
                                $description_objet,
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

    public function recherche($id)
    {
       $recherche = DB::select(
        'SELECT *
        FROM biens
        WHERE id_objet = ?',
        [$id]);

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
                'message' => 'voici les informations sur les biens'

            ], 200
        );
    }


}
