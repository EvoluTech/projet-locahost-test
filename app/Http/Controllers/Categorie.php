<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class Categorie extends Controller
{
    public function insertionCategorie(Request $request) {
        $rules = [
            'id_categorie' => 'required|max:5',
            'nom_categorie' => 'required',
            'plage_prix' => 'required',
            'prix_categorie' => 'required',
            'id_user' => 'required',
        ];
    
        $messages = [
            'required' => '{Veuillez saisir le :attribute}',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ], 401);
        }
        
        $id_categorie = $request->input('id_categorie');
        $nom_categorie = strtoupper($request->input('nom_categorie'));
        $plage_prix = $request->input('plage_prix');
        $prix_categorie = $request->input('prix_categorie');
        $id_user = $request->input('id_user');

        $insertCat=DB::insert("INSERT INTO public.categorie(
            id_categorie, nom_categorie, plage_prix, prix_categorie, id_user)
            VALUES (?,?,?,?,?);",[$id_categorie,$nom_categorie,$plage_prix,$prix_categorie,$id_user]);

            if ($insertCat){
                return response()->json(
                    [
                        'message' => 'Insertion de la catégorie réussie',
                        'status' => true
                    ],

                200);
            }

            return response()->json(['error' => 'Vérifier votre code'],401);
    }

    public function majCategorie(Request $request, $id_categorie) {
        $validator = 
        [
            'nom_categorie'  => 'required',
            'plage_prix'     => 'required',
            'prix_categorie' => 'required',
            'id_user'        => 'required'
        ];

        $messages = [
            'required' => ' Veuillez saisir le :attribute',
        ];

        $validator = Validator::make($request->all(), $validator,$messages);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
            400);
        }

        $nom_categorie = $request->input('nom_categorie');
        $plage_prix = $request->input('plage_prix');
        $prix_categorie = $request->input('prix_categorie');
        $id_user = $request->input('id_user');

        $modification=DB::update("UPDATE public.categorie
        SET nom_categorie=?, plage_prix=?, prix_categorie=?, id_user=?
        WHERE id_categorie=?",
        [$nom_categorie,$plage_prix,$prix_categorie,$id_user,$id_categorie]);

        if ($modification){
            return response()->json(
                [
                    'message' => 'Réussie',
                    'status' => true,
                ], 
                
            200);
        }

        return response()->json(['error' => 'Echec'], 401);
    }

    public function supprimerCategorie($id) {
        $verfie=DB::select
            (
                'select *
                from categorie
                where id_categorie = ? ', [$id]
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
                from categorie
                where id_categorie = ?', [$id]
            );
    
            return response()->json (
                [
                    'status'=>true,
                    'message'=>"Suppression réussie !"
                ], 200
                );
        }
    }

    public function listeCategorie($id) {
        $recherche=DB::select
            (
                'SELECT *
                FROM categorie
                WHERE id_categorie = ? ', [$id]
            );

        if (empty($recherche)) {
    
            return response()->json (
                    [
                        'status'=> false,
                        'message'=>"ID inexistant"
                    ], 400
            );
        }
            return response()->json (
                    [
                        'status'  => true,
                        'message' => "Réussie",
                        'data' => $recherche
                    ], 200
        );
    }

}