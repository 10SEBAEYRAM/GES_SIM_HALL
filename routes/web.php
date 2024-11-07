<?php

use App\Http\Controllers\ProduitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypeTransactionController;
use App\Http\Controllers\GrilleTarifaireController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\CaisseController;
Route::resource('caisses', CaisseController::class);
Route::get('caisses/{id_caisse}/edit', [CaisseController::class, 'edit'])->name('caisses.edit');

// Route publique pour les types de transactions
Route::get('/type-transactions', [TypeTransactionController::class, 'index'])
    ->name('type-transactions.index');

// Page d'accueil redirige vers login
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Groupe de routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    
    // Gestion du profil
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
    
    // Ressources principales
    Route::resources([
        'type-transactions' => TypeTransactionController::class,
        'produits' => ProduitController::class,
        'grille-tarifaires' => GrilleTarifaireController::class,
        'transactions' => TransactionController::class,
    ]);
    
    // Route spécifique pour le calcul de commission
    Route::get('/transactions/get-commission', [TransactionController::class, 'getCommission'])
        ->name('transactions.get-commission');
});

// Routes d'authentification
require __DIR__.'/auth.php';