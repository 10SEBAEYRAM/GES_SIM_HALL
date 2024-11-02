<?php

use App\Http\Controllers\UserController; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route; 
use App\Models\User;

// Routes pour le gestionnaire d'utilisateurs
Route::resource('users', UserController::class);

// Middleware pour les utilisateurs authentifiés
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route pour le tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('verified');
});

// Page d'accueil du site
Route::get('/', function () {
    return view('welcome'); 
});

// Route pour tester les utilisateurs (facultatif)
Route::get('/test-users', function () {
    return User::all(); 
});

// Routes d'authentification
require __DIR__.'/auth.php';
