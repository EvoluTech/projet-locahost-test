<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApi;
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

Route::post('/login', [TestApi::class, 'login']);
Route::post('/creerCompte', [TestApi::class, 'creerCompte']);
Route::delete('/delete/{id_user}', [TestApi::class, 'destroy']);
Route::put('/update/{id_user}', [TestApi::class, 'update']);
Route::get('/select', [TestApi::class, 'select']);

Route::post('/creerBien', [Biens::class, 'creerBien']);
Route::delete('/suprimer/{id_objet}', [Biens::class, 'suprimer']);
Route::put('/maj/{id_objet}', [Biens::class, 'maj']);
Route::get('recherche/{id_objet}',  [Biens::class, 'recherche']);

Route::middleware('verify.token')->group(function(){



    Route::post('/testProtectedRoute', [TestApi::class, 'testProtectedRoute']);

});

