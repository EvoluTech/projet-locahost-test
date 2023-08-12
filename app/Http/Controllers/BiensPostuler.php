<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Detailsbienspostuler;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BiensPostuler extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }

  /*  public function create(Request $request)
    {
        // Validation des données entrantes pour Detailsbienspostuler
        $detailsValidator = Validator::make($request->all(), [
            // Règles de validation pour Detailsbienspostuler
            'nombre_vue_detailsbienspostuler' => 'required',
            'signalisation_detailsbienspostuler' => 'required',
            'status_detailsbienspostuler' => 'required'
         
        ]);

        if ($detailsValidator->fails()) {
            return response()->json([
                'error' => $detailsValidator->errors()->all()
            ], 400);
        }

        // Insérer dans Detailsbienspostuler
        $detailsId = DB::table('Detailsbienspostuler')->insertGetId([ 
            // Champs à insérer pour Detailsbienspostuler
        ]);

        if (!$detailsId) {
            return response()->json([
                'error' => 'Échec de l\'insertion dans Detailsbienspostuler'
            ], 500);
        }

        // Validation des données entrantes pour BiensPostuler
        $bienValidator = Validator::make($request->all(), [
            // Règles de validation pour BiensPostuler
        ]);

        if ($bienValidator->fails()) {
            // Supprimer l'entrée précédente dans Detailsbienspostuler en cas d'erreur
            DB::table('Detailsbienspostuler')->where('id_detailsbienspostuler', $detailsId)->delete();

            return response()->json([
                'error' => $bienValidator->errors()->all()
            ], 400);
        }

        // Insérer dans BiensPostuler avec l'ID de Detailsbienspostuler
        $bienPostulerId = DB::table('BiensPostuler')->insertGetId([
            'id_detailsbienspostuler' => $detailsId,
            // Champs à insérer pour BiensPostuler
        ]);

        if (!$bienPostulerId) {
            // Supprimer les entrées précédentes en cas d'erreur
            DB::table('Detailsbienspostuler')->where('id_detailsbienspostuler', $detailsId)->delete();

            return response()->json([
                'error' => 'Échec de l\'insertion dans BiensPostuler'
            ], 500);
        }

        return response()->json([
            'message' => 'Insertion réussie dans Detailsbienspostuler et BiensPostuler',
            'status' => true
        ], 200);
}*/


public function create(Request $request)
{
    // Validation des données et insertion dans Detailsbienspostuler
    $detailsData = [
        'nombre_vue_detailsbienspostuler' => 0,
        'signalisation_detailsbienspostuler' => 0,
        'status_detailsbienspostuler' => 0,
    ];
    $details = Detailsbienspostuler::create($detailsData);

    // Utilisation de l'ID généré pour insérer dans Bienspostuler
    $biensData = [
        'id_bienspostuler' => 'valeur_id_bienspostuler',
        'date_debut_postule' => '2023-07-16 00:00:00', // Mettez la date appropriée
        'date_fin_postule' => '2023-07-20 00:00:00', // Mettez la date appropriée
        'prix_biens' => 'valeur_prix_biens',
        'prix_par_jour' => 'valeur_prix_par_jour',
        'prix_total_payer' => 'valeur_prix_total_payer',
        /*'photos_1' => 'chemin_photo_1',
        'photos_2' => 'chemin_photo_2',
        'photos_3' => 'chemin_photo_3',
        'photos_4' => 'chemin_photo_4',*/
        'etat_biens' => 'valeur_etat_biens',
        'description_biens' => 'valeur_description_biens',
        'id_detailsbienspostuler' => $details->id_detailsbienspostuler,
        'id_objet' => 'valeur_id_objet',
    ];

    $imagePaths = [];
    foreach(['photos_1','photos_2','photos_3','photos_4'] as $photoField){
        if ($request->hasFile($photoField)) {
            $image = $request->file($photoField);
            $imagePath = $image->store('uploads', 'public');
            $imagePaths[$photoField] = $imagePath;
        }
    }

    $biensData = array_merge($biensData, $imagePaths);

    $bienspostuler = BiensPostuler::create($biensData);

    return response()->json([
        'message' => 'Enregistrement réussi',
        'details' => $details,
        'bienspostuler' => $bienspostuler,
    ], 200);
}
}

