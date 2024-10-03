<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('parties')
    ->controller(PartieController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
       Route::post('/', 'nouvellePartie');
       Route::post('/{partie}/missiles', 'tirerMissile');
       Route::put('/{partie}/missiles/{coordonees}', 'recevoirTire');
       Route::delete('/{partie}', 'destroy');
    });
