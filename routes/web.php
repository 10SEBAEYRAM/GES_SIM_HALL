<?php

use App\Http\Controllers\UserController; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;use App\Models\User;Route::resource('users', UserController::class);
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
   Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Page d'accueil du site
Route::get('/', function () {
    return view('welcome'); 
});

// Route pour le tableau de bord
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/test-users', function () {
    return User::all(); 
});

// Routes d'authentification
require __DIR__.'/auth.php'; 
