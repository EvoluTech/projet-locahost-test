<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApi;
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

Route::post('/creerCompte', [TestApi::class, 'creerCompte']);
Route::put('/modifierCompte/{id_user}', [TestApi::class, 'modifierCompte']);
Route::delete('/suprimer/{id_user}', [TestApi::class, 'suprimer']);

Route::post('/insertionCategorie', [Categorie::class, 'insertionCategorie']);
Route::put('/majCategorie/{id_categorie}', [Categorie::class, 'majCategorie']);
Route::delete('/supprimerCategorie/{id_categorie}', [Categorie::class, 'supprimerCategorie']);
Route::get('/rechercheCategorie/{id_categorie}', [Categorie::class, 'rechercheCategorie']);

Route::middleware('verify.token')->group(function() {
    
});



