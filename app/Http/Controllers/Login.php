<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class Login extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function __construct()
    {
        //middleware : anelan'elan'ny controller sy route,eto zao token ny middleware
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(Request $request) {
        //Validation des données entrantes
        $rules = [
            'nom_user' => 'required|max:10',
            'prenom_user' => 'required',
            'mdp_user' => 'required|min:8',
            'type_user' => 'required',
            'adresse_user' => 'required',
            'adresse_mail' => 'required|email',
        ];

        $messages = [
            'required' => ' Veuillez saisir le :attribute',
            'max'      => ' Vérifier la longueur de :attribute',
            'min'      => ' Vérifier la taille :attribute',
            'email'    => ' Incorrect :attribute',
            'integer'  => ' Veuillez saisir en entier :attribute',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
            400);
        }

        $nom_user = strtoupper($request->input('nom_user'));
        $prenom_user = $request->input('prenom_user');
        $mdp_user = $request->input('mdp_user');
        $type_user = $request->input('type_user');
        $adresse_user = $request->input('adresse_user');
        $adresse_mail = $request->input('adresse_mail');

        $pdp = 'uploads/default.jpg';

        $register=DB::insert("INSERT INTO public.users(
            id_user, nom_user, prenom_user, mdp_user, type_user, adresse_user, adresse_mail,pdp)
            VALUES (nextval('id_user'),?,?,?,?,?,?,?)
            ",[$nom_user,$prenom_user,$mdp_user,$type_user,$adresse_user,$adresse_mail,$pdp]);
            

        if ($register) {
            return response()->json(
                [
                    'message' => 'Inscription réussie',
                    'status' => true
                ],

            200);
        } 
        return response()->json(
            [
                'error' => 'Inscription échouée'
            ], 
            
            401);
        
    }

    public function login(Request $request)
    {
        // Validation des données entrantes
        $validator = 
        [
            'adresse_mail' => 'required|email',
            'mdp_user' => 'required|min:8',
        ];

        $message = [
            'required' => ' Veuillez saisir le :attribute',
            'email'    => ' Vérifier votre :attribute',
            'min'      => ' Vérifier la longueur de votre :attribute',
        ];

        $validator = Validator::make($request->all(), $validator,$message);
    
        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
            400);
        }

        $adresse_mail = $request->input('adresse_mail');
        $mdp_user = $request->input('mdp_user');

        $user = User::where('adresse_mail', $adresse_mail)->first();

        if (!$user) {
            return response()->json(
                [
                    'error' => 'Adresse e-mail incorrecte',
                    'status' => false
                ],
                401
            );
        }
    
        if ($mdp_user !== $user->mdp_user) {
            return response()->json(
                [
                    'error' => 'Mot de passe incorrect',
                    'status' => false
                ],
                401
            );
        }
            $token = $this->guard()->login($user);
            
            return response()->json(
            [
                'token' => $token,
                'message' => 'Bienvenue',
                'status' => true
            ], 
            
        200);
        
    }


}
