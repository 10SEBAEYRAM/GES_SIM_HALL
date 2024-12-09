<?php

use App\Http\Controllers\{
    ProduitController,
    UserController,
    ProfileController,
    TypeTransactionController,
    GrilleTarifaireController,
    TransactionController,
    CaisseController,
    DashboardController
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

    // Gestion des produits
    Route::middleware(['auth'])->group(function () {
        // Routes existantes...
        Route::resource('produits', ProduitController::class)->except(['show']);
        Route::get('/produits/datatable', [ProduitController::class, 'getDatatable'])->name('produits.data');
    });

    // Gestion des transactions

    Route::resource('transactions', TransactionController::class);

    // Routes personnalisées pour les transactions
    Route::patch('transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])
        ->name('transactions.updateStatus');

    Route::get('transactions/search', [TransactionController::class, 'search'])
        ->name('transactions.search');

    Route::get('transactions/export', [TransactionController::class, 'export'])
        ->name('transactions.export');

    Route::get('/transactions/get-commission', [TransactionController::class, 'getCommission'])
        ->name('transactions.get-commission');


    // Gestion de la grille tarifaire
    Route::resource('grille-tarifaires', GrilleTarifaireController::class);

    // Gestion du profil
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Gestion des caisses
    Route::middleware(['auth'])->group(function () {
        // Afficher la liste des caisses
        Route::get('/caisses', [CaisseController::class, 'index'])->name('caisses.index');

        // Créer une nouvelle caisse
        Route::get('/caisses/create', [CaisseController::class, 'create'])->name('caisses.create');
        Route::post('/caisses', [CaisseController::class, 'store'])->name('caisses.store');

        // Modifier une caisse existante
        Route::get('/caisses/{id}/edit', [CaisseController::class, 'edit'])->name('caisses.edit');
        Route::put('/caisses/{id}', [CaisseController::class, 'update'])->name('caisses.update');

        // Supprimer une caisse
        Route::delete('/caisses/destroy', [CaisseController::class, 'destroy'])->name('caisses.destroy');
    });

    // Gestion des types de transactions
    Route::resource('type-transactions', TypeTransactionController::class);
});

// Routes d'authentification
require __DIR__ . '/auth.php';
