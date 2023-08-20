<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApi;
use App\Http\Controllers\Biens;
use App\Http\Controllers\BiensPostuler;
use App\Http\Controllers\Categorie;
use App\Http\Controllers\Login;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::post('/login', [TestApi::class, 'login']);
Route::post('/login', [Login::class, 'login']);
Route::post('/register', [Login::class, 'register']);

//Controlleur Categorie
Route::post('/insertionCategorie', [Categorie::class, 'insertionCategorie']);
Route::put('/majCategorie/{id_categorie}', [Categorie::class, 'majCategorie']);
Route::delete('/supprimerCategorie/{id_categorie}', [Categorie::class, 'supprimerCategorie']);
Route::get('/rechercheCategorie/{id_categorie}', [Categorie::class, 'rechercheCategorie']);


//Controlleur BiensPostuler
Route::post('/createPostuler', [BiensPostuler::class, 'createPostuler']);
Route::get('/getBienPostulerData', [BiensPostuler::class, 'getBienPostulerData']);
Route::get('/getSingleBienPostulerData/{id_bienspostuler}', [BiensPostuler::class, 'getSingleBienPostulerData']);
Route::get('/getFiltreBienPostulerData/{id_objet}', [BiensPostuler::class, 'getFiltreBienPostulerData']);
Route::get('/getMesBienPostulerData/{id_user}', [BiensPostuler::class, 'getMesBienPostulerData']);

//Controlleur Biens
Route::post('/creerBien', [Biens::class, 'creerBien']);
Route::delete('/suprimer/{id_objet}', [Biens::class, 'suprimer']);
Route::put('/maj/{id_objet}', [Biens::class, 'maj']);
Route::get('recherche/{id_objet}',  [Biens::class, 'recherche']);
Route::get('listBien',  [Biens::class, 'listBien']);



// Route::post('/createPostuler', [BiensPostuler::class, 'createPostuler']);
Route::post('/reagir', [BiensPostuler::class, 'reagir']);


Route::middleware('verify.token')->group(function(){
    Route::post('/testProtectedRoute', [TestApi::class, 'testProtectedRoute']);

});

