<?php

use App\Http\Controllers\ProduitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypeTransactionController;
use App\Http\Controllers\GrilleTarifaireController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Models\User;


// Middleware pour les utilisateurs authentifiés
Route::middleware(['auth'])->group(function () {    
    // Routes pour les utilisateurs (déjà existantes)
    Route::resource('users', UserController::class);
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Routes pour le profil d'utilisateur

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les types de transactions
    Route::resource('type-transactions', TypeTransactionController::class);

    // Routes pour les produits
    Route::resource('produits', ProduitController::class);

    // Routes pour les grilles tarifaires
    Route::resource('grille-tarifaires', GrilleTarifaireController::class);

    // Routes pour les transactions
    Route::get('/transactions/get-commission', [TransactionController::class, 'getCommission'])
         ->name('transactions.get-commission');
    Route::resource('transactions', TransactionController::class);

});

// Page d'accueil du site
Route::get('/', function () {
    return view('auth.login'); 
});



// Routes d'authentification
require __DIR__.'/auth.php';
