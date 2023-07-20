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
            'required' => '{:attribute: Veuillez saisir le :attribute}',
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
            VALUES ('".$id_categorie."', '".$nom_categorie."', '".$plage_prix."', '".$prix_categorie."', '".$id_user."');");

            if ($insertCat){
                return response()->json(
                    [
                        'message' => 'Insertion de la catégorie réussie',
                        'status' => true
                    ],

                200);
            }

            return response()->json(['error' => 'Vérifier votre code ahahaha'],401);
    }
}
