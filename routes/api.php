<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApi;
use App\Http\Controllers\BiensPostuler;
use App\Http\Controllers\Categorie;
use App\Http\Controllers\Login;
use App\Http\Controllers\Biens;
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

Route::post('/creerCompte', [TestApi::class, 'creerCompte']);
Route::post('/uploadImage', [TestApi::class, 'uploadImage']);
Route::put('/modifierCompte/{id_user}', [TestApi::class, 'modifierCompte']);
Route::delete('/suprimer/{id_user}', [TestApi::class, 'suprimer']);
Route::get('/search/{id_user}/{id_objet}', [TestApi::class, 'search']);
Route::get('/searchpub/{id_user}', [TestApi::class, 'searchpub']);

Route::post('/insertionCategorie', [Categorie::class, 'insertionCategorie']);
Route::put('/majCategorie/{id_categorie}', [Categorie::class, 'majCategorie']);
Route::delete('/supprimerCategorie/{id_categorie}', [Categorie::class, 'supprimerCategorie']);
Route::get('/rechercheCategorie/{id_categorie}', [Categorie::class, 'rechercheCategorie']);

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


Route::post('/creerBien', [Biens::class, 'creerBien']);
Route::delete('/suprimer/{id_objet}', [Biens::class, 'suprimer']);
Route::put('/maj/{id_objet}', [Biens::class, 'maj']);
Route::get('recherche/{id_objet}',  [Biens::class, 'recherche']);

Route::middleware('verify.token')->group(function(){



    Route::post('/testProtectedRoute', [TestApi::class, 'testProtectedRoute']);

});

