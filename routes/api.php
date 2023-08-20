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


Route::post('/insertionCategorie', [Categorie::class, 'insertionCategorie']);
Route::put('/majCategorie/{id_categorie}', [Categorie::class, 'majCategorie']);
Route::delete('/supprimerCategorie/{id_categorie}', [Categorie::class, 'supprimerCategorie']);
Route::get('/rechercheCategorie/{id_categorie}', [Categorie::class, 'rechercheCategorie']);

Route::middleware('verify.token')->group(function() { });



Route::post('/createPostuler', [BiensPostuler::class, 'createPostuler']);

Route::middleware('verify.token')->group(function() {

});


Route::post('/login', [TestApi::class, 'login']);
Route::post('/creerCompte', [TestApi::class, 'creerCompte']);
Route::delete('/delete/{id_user}', [TestApi::class, 'destroy']);
Route::put('/update/{id_user}', [TestApi::class, 'update']);
Route::get('/select', [TestApi::class, 'select']);
Route::get('/getNextSequenceValue',[TestApi::class, 'getNextSequenceValue']);
Route::post('/BiensPostuler', [TestApi::class, 'BiensPostuler']);
Route::post('/postuler', [TestApi::class, 'postuler']);
Route::post('/reagir', [TestApi::class, 'reagir']);


Route::post('/creerBien', [Biens::class, 'creerBien']);
Route::delete('/suprimer/{id_objet}', [Biens::class, 'suprimer']);
Route::put('/maj/{id_objet}', [Biens::class, 'maj']);
Route::get('recherche/{id_objet}',  [Biens::class, 'recherche']);



Route::post('/createPostuler', [BiensPostuler::class, 'createPostuler']);
Route::post('/reagir', [BiensPostuler::class, 'reagir']);


Route::middleware('verify.token')->group(function(){



    Route::post('/testProtectedRoute', [TestApi::class, 'testProtectedRoute']);

});

