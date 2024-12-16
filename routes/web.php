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

Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('products', ProduitController::class);
    Route::resource('transactions', TransactionController::class);
    Route::get('manage-caisse', [CaisseController::class, 'index']);
    Route::get('manage-tarifaire', [GrilleTarifaireController::class, 'index']);
});

Route::middleware(['role:operator'])->group(function () {
    Route::resource('transactions', TransactionController::class)->only(['create', 'edit', 'store']);
});
// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Gestion du tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/api/dashboard', [DashboardController::class, 'getFilteredData']);
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    Route::get('/dashboard/filter', [DashboardController::class, 'filter']);

    Route::get('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
    Route::get('/caisses/caisse_transactions/create', [TransactionController::class, 'create'])
        ->name('caisses.caisse_transactions.create');
    Route::post('/caisses/caisse_transactions/store', [TransactionController::class, 'store'])
        ->name('caisses.caisse_transactions.store');

    // Gestion des produits
    Route::middleware(['auth'])->group(function () {
        // Routes existantes...
        Route::resource('produits', ProduitController::class)->except(['show']);
        Route::get('/produits/datatable', [ProduitController::class, 'getDatatable'])->name('produits.data');
    });


    // Affiche le formulaire de création d'un mouvement (méthode GET)
    Route::prefix('caisses')->name('caisses.')->group(function () {
        Route::get('/', [CaisseController::class, 'index'])->name('index');
        Route::get('mouvements/create', [CaisseController::class, 'createMouvement'])->name('mouvements.create');
        Route::post('mouvements/store', [CaisseController::class, 'storeMouvement'])->name('mouvements.store');
        // Nouvelle route pour afficher les détails

        // Route pour les mouvements de caisse
        Route::post('/caisses/mouvement', [CaisseController::class, 'storeMouvement'])->name('caisses.mouvement.store');
    });

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
    Route::resource('grille_tarifaires', GrilleTarifaireController::class);


    // Gestion du profil
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Gestion des caisses
    Route::prefix('caisses')->name('caisses.')->group(function () {
        Route::get('/', [CaisseController::class, 'index'])->name('index');
        Route::get('/create', [CaisseController::class, 'create'])->name('create');
        Route::post('/', [CaisseController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CaisseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CaisseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CaisseController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/show', [CaisseController::class, 'show'])->name('show');
    });


    // Gestion des types de transactions
    Route::resource('type-transactions', TypeTransactionController::class);

    Route::get('type-transactions/{id}/edit', [TypeTransactionController::class, 'edit'])->name('type-transactions.edit');
    Route::get('type-transaction', [TypeTransactionController::class, 'index'])->name('type-transaction.index');
    Route::get('types-transaction', [TypeTransactionController::class, 'index'])->name('types-transaction.index');
});

// Routes d'authentification
require __DIR__ . '/auth.php';

Route::get('/api/dashboard-data', [DashboardController::class, 'getDashboardData']);
