<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Categorie extends Controller
{
    public function insertionCategorie(Request $request) {
        $id_categorie = $request->input('id_categorie');
        $nom_categorie = $request->input('nom_categorie');
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
