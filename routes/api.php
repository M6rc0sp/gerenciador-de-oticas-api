<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NuvemshopController;
use Illuminate\Support\Facades\Route;

// Rota de instalação do app Nuvemshop (pública, sem autenticação)
Route::get('/ns/install', [NuvemshopController::class, 'install']);

// Rotas protegidas por autenticação Nexo
Route::get('/{store_id}/categories', [CategoryController::class, 'apiIndex']);
Route::middleware('nexo.auth')->group(function () {
    Route::get('/categories', [CategoryController::class, 'apiIndexAdmin']);
    Route::post('/categories', [CategoryController::class, 'apiStore']);
    Route::get('/categories/{category}', [CategoryController::class, 'apiShow']);
    Route::put('/categories/{category}', [CategoryController::class, 'apiUpdate']);
    Route::delete('/categories/{category}', [CategoryController::class, 'apiDestroy']);
    Route::patch('/categories/{category}/reorder', [CategoryController::class, 'apiReorder']);
});
