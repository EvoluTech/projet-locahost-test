<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class BiensPostuler extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function createPostuler(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_debut_postule' => 'required',
            'date_fin_postule' => 'required',
            'prix_biens' => 'required',
            'prix_par_jour' => 'required',
            'prix_total_payer' => 'required',
            'description_biens' => 'required',
            'ville' => 'required',
            'id_objet' => 'required',
            'type_annee' => 'required',
            'adresse_bien' => 'required',
            'pub_bien' => 'required',
            'id_user' => 'required',
        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'string' => 'Le champ :attribute doit être une chaîne de caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date_debut_postule = $this->convStringTimestamp($request->input('date_debut_postule'));
        $date_fin_postule = $this->convStringTimestamp($request->input('date_fin_postule'));
        $date_postuler = Carbon::now()->toDateTimeString();

        $prix_biens = $request->input('prix_biens');
        $prix_par_jour = $request->input('prix_par_jour');
        $prix_total_payer = $request->input('prix_total_payer');
        $etat_biens ='0';
        $description_biens = $request->input('description_biens');
        $ville = $request->input('ville');
        $id_objet = $request->input('id_objet');
        $type_annee = $request->input('type_annee');
        $adresse_bien = $request->input('adresse_bien');
        $pub_bien = $request->input('pub_bien');
        $id_user = $request->input('id_user');

        // Insertion dans la table "detailsbienspostuler"
        $sequence_id_detail_bien = $this->getNextSequenceValue('id_detail_bien_postuler');
        $nombre_vue_detailsbienspostuler = 0;
        $signalisation_detailsbienspostuler = 0;
        $status_detailsbienspostuler = 0;

        $dataDetailsB = [
            $sequence_id_detail_bien,
            $nombre_vue_detailsbienspostuler,
            $signalisation_detailsbienspostuler,
            $status_detailsbienspostuler,
        ];

        $sqlInsertDetailsB = "INSERT INTO public.detailsbienspostuler(id_detailsbienspostuler,nombre_vue_detailsbienspostuler,signalisation_detailsbienspostuler,status_detailsbienspostuler) values (?,?,?,?)";

        try {
            $insertDetailsB = DB::insert($sqlInsertDetailsB, $dataDetailsB);

            // Enregistrement des images
            $imagePaths = $this->storeImages($request);

            // Insertion dans la table "bienspostuler"
            $sequence_id_bien_postuler = $this->getNextSequenceValue('id_bien_postuler');

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
                    adresse_bien,
                    pub_bien,
                    photos_1,
                    photos_2,
                    photos_3,
                    photos_4)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $sequence_id_bien_postuler,
                    $date_debut_postule,
                    $date_fin_postule,
                    $prix_biens,
                    $prix_par_jour,
                    $prix_total_payer,
                    $etat_biens,
                    $description_biens,
                    $ville,
                    $id_objet,
                    $sequence_id_detail_bien,
                    $type_annee,
                    $adresse_bien,
                    $pub_bien,
                    $imagePaths['photos_1'],
                    $imagePaths['photos_2'],
                    $imagePaths['photos_3'],
                    $imagePaths['photos_4'],
                ] 
            );

            // // Insertion dans la table "postuler"
            $dataPostuler = [$id_user, $sequence_id_bien_postuler, $date_postuler];
            $sqlInsertPostuler = "INSERT INTO public.postuler(id_user, id_bienspostuler, date_postuler) values(?,?,?)";
            $inserta = DB::insert($sqlInsertPostuler, $dataPostuler);

            return response()->json([
                "status" => true,
                "message" => "Insertion réussie",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "erreur" => $th,
                "message" => "Insertion non réussie",
            ], 400);
        }
    }

    public function reagir(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_user'=> 'required|integer',
            'id_bienspostuler' => 'required',
        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'integer' => 'Le champ :attribute doit être un entier naturel.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id_user = $request->input('id_user');
        $id_bienspostuler = $request->input('id_bienspostuler');
        $date_reaction = Carbon::now()->toDateTimeString();
        $varSelec = 'select * from reagir where id_bienspostuler=? and id_user=?';

        try {
            $reqSelect = DB::select($varSelec, [$id_bienspostuler, $id_user]);
            if (!$reqSelect) {
                $insertion = DB::insert(
                    "INSERT INTO public.reagir(
                        id_user,
                        id_bienspostuler,
                        date_reaction)
                    VALUES (?, ?, ?)",
                    [$id_user, $id_bienspostuler, $date_reaction]
                );

                return response()->json([
                    "status" => true,
                    "message" => "Like"
                ], 200);
            } else {
                $delete = DB::delete('delete from reagir where id_bienspostuler=? and id_user=?', [$id_bienspostuler, $id_user]);

                return response()->json([
                    "status" => true,
                    "message" => "Tsy like"
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "erreur" => $th,
                "message" => "Insertion non réussit"
            ], 400);
        }
    }

    // Fonction pour convertir une chaîne de caractères en timestamp
    private function convStringTimestamp($dateString) {
        return Carbon::createFromFormat('d/m/Y', $dateString);
    }

    // Fonction pour obtenir la prochaine valeur de séquence
    private function getNextSequenceValue($sequenceName) {
        $result = DB::select("SELECT nextval('$sequenceName') as next_value");
        return $result[0]->next_value;
    }

    // Fonction pour stocker les images et renvoyer leurs chemins
    private function storeImages(Request $request) {
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            $currentTimestamp = Carbon::now()->timestamp . "" . $i;
            if ($request->hasFile("photos_$i")) {
                $image = $request->file("photos_$i");
                $imageName = $currentTimestamp . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('uploads', $imageName, 'public');
                $imagePaths["photos_$i"] = $imagePath;
            }
        }
        return $imagePaths;
    }
}
