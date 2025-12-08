<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação (públicas)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::redirect('/', '/categories');

    // Categorias (Interface Web)
    Route::resource('categories', CategoryController::class);

    // Endpoint para reordenar categorias (drag-and-drop)
    Route::patch('/categories/{category}/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // API para JSON (front-end consumption)
    Route::get('/api/categories/json', [CategoryController::class, 'getJson'])->name('categories.json');
});

// Dev-only debug preview (bypass auth) - only in local environment
if (app()->isLocal()) {
    Route::get('/debug/categories', function () {
        $categories = App\Models\Category::root()->with('children.children')->get();

        return view('categories.index', compact('categories'));
    });
}
