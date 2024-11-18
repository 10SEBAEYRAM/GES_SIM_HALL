<?php

use App\Http\Controllers\{
    ProduitController, UserController, ProfileController, 
    TypeTransactionController, GrilleTarifaireController, 
    TransactionController, CaisseController, DashboardController
};
use Illuminate\Support\Facades\Route;

// Page d'accueil redirige vers login
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Gestion du tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Gestion des utilisateurs
    Route::resource('users', UserController::class);

    // Gestion des transactions avec routes supplémentaires
    Route::resource('transactions', TransactionController::class);
    Route::patch('transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])
        ->name('transactions.updateStatus');
    Route::get('transactions/search', [TransactionController::class, 'search'])->name('transactions.search');
    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');

    Route::get('/transactions/get-commission', [TransactionController::class, 'getCommission'])
        ->name('transactions.get-commission');

    // Gestion des produits
    Route::resource('produits', ProduitController::class)->except(['update']);
    Route::put('/produits/{id_prod}', [ProduitController::class, 'update'])->name('produits.update');

    // Gestion de la grille tarifaire
    Route::resource('grille-tarifaires', GrilleTarifaireController::class);

    // Gestion du profil
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Autres ressources
    Route::resource('type-transactions', TypeTransactionController::class);
    Route::resource('caisses', CaisseController::class);
});

// Routes d'authentification
require __DIR__.'/auth.php';
