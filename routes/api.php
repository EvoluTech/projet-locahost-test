<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApi;
use App\Http\Controllers\Categorie;
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
Route::post('/login', [TestApi::class, 'login']);
Route::post('/creerCompte', [TestApi::class, 'creerCompte']);
Route::post('/insertionCategorie', [Categorie::class, 'insertionCategorie']);
Route::middleware('verify.token')->group(function() {

});



