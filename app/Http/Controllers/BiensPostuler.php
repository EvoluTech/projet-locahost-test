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

    function convStringTimestamp($dateString) {
        $date = Carbon::createFromFormat('d/m/Y', $dateString);
        return $date;
    }

    public function createPostuler(Request $request)
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
                'adresse_bien' => 'required',
                'pub_bien' => 'required',

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
            $adresse_bien = $request->input('adresse_bien');
            $pub_bien = $request->input('pub_bien');
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
                        adresse_bien,
                        pub_bien,
                        photos_1,
                        photos_2,
                        photos_3,
                        photos_4)
                    VALUES (
                        '$sequennce_id_bien_postuler',
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
                        '$adresse_bien',
                        '$pub_bien',
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




    public function reagir(Request $request) {
        $validator = Validator::make($request->all(), [
                'id_user'=> 'required|integer',
                'id_bienspostuler' => 'required',
                ], [
                'required' => 'Le champ :attribute est obligatoire.',
                'integer' => 'Le champ :attribute doit être un entier naturel.',
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
        $id_bienspostuler = $request->input('id_bienspostuler');
        $date_reaction = Carbon::now()->toDateTimeString();
        $varSelec='select * from reagir where id_bienspostuler=? and id_user=?';
        try
        {
            $reqSelect=DB::select($varSelec, [$id_bienspostuler,$id_user]);
            if (!$reqSelect) {
                $insertion=DB::insert(
                    "INSERT INTO public.reagir(
                        id_user,
                        id_bienspostuler,
                        date_reaction)
                    VALUES (
                        '".$id_user."',
                        '".$id_bienspostuler."',
                        '".$date_reaction."'
                    )");

                    return  response()->json
                    (
                        [
                            "status" => true,
                            "message"=> "Like"
                        ], 200
                    );
            }
            else{
                $delete=DB::delete('delete from reagir where id_bienspostuler=? and id_user=?', [$id_bienspostuler,$id_user]);
                return  response()->json
                (
                    [
                        "status" => true,
                        "message"=> "Tsy like"
                    ], 200
                );
            }

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





}

