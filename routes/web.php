<?php

use App\Http\Controllers\{
    ProduitController,
    UserController,
    ProfileController,
    TypeTransactionController,
    GrilleTarifaireController,
    TransactionController,
    CaisseController,
    DashboardController,
    MouvementProduitController
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;


// Page d'accueil redirige vers login
Route::get('/', function () {
    Log::channel('single')->info('Test log info');
    Log::channel('single')->emergency('Test log emergency');
    Log::alert('Test log alert');
    Log::critical('Test log critical');
    Log::error('Test log error');
    Log::warning('Test log warning');
    Log::notice('Test log notice');
    Log::debug('Test log debug');
    return view('auth.login');
})->name('home');

Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('products', ProduitController::class);
    Route::resource('transactions', TransactionController::class);
    Route::get('manage-caisse', [CaisseController::class, 'index']);
    Route::get('manage-tarifaire', [GrilleTarifaireController::class, 'index']);
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/caisses/{caisse}/operations-non-remboursees', 'CaisseController@getOperationsNonRemboursees');
    // Other routes...
});
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/caisses/{caisse}/operations-non-remboursees', 'CaisseController@getOperationsNonRemboursees');
});
Route::middleware(['role:operator'])->group(function () {
    Route::resource('transactions', TransactionController::class)->only(['create', 'edit', 'store']);
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Route pour le calcul de commission (doit être avant les routes resource)
    Route::get('/api/commission/calculate', [TransactionController::class, 'getCommission'])
        ->name('transactions.get-commission');

    // Gestion du tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Route pour le calcul de commission
    Route::get('/calculate-commission', [GrilleTarifaireController::class, 'commissionGrilleTarifaire'])
        ->name('commission.calculate');

    Route::get('/api/dashboard', [DashboardController::class, 'getFilteredData']);
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
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
        Route::get('/create', [CaisseController::class, 'create'])->name('create');
        Route::post('/', [CaisseController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CaisseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CaisseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CaisseController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/show', [CaisseController::class, 'show'])->name('show');

        // Ajouter ces routes pour les mouvements
        Route::get('/mouvements/create', [CaisseController::class, 'createMouvement'])->name('mouvements.create');
        Route::post('/mouvements', [CaisseController::class, 'storeMouvement'])->name('mouvements.store');
    });

    Route::resource('transactions', TransactionController::class);

    // Routes personnalisées pour les transactions
    Route::patch('transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])
        ->name('transactions.updateStatus');

    Route::get('transactions/search', [TransactionController::class, 'search'])
        ->name('transactions.search');

    Route::get('transactions/export', [TransactionController::class, 'export'])
        ->name('transactions.export');

    // Gestion de la grille tarifaire
    Route::resource('grille_tarifaires', GrilleTarifaireController::class);

    Route::middleware(['auth'])->group(function () {
        Route::resource('grille-tarifaires', GrilleTarifaireController::class);
        Route::get('grille-tarifaires/data', [GrilleTarifaireController::class, 'getData'])->name('grille-tarifaires.data');
    });

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

        // Ajouter ces routes pour les mouvements
        Route::get('/mouvements/create', [CaisseController::class, 'createMouvement'])->name('mouvements.create');
        Route::post('/mouvements', [CaisseController::class, 'storeMouvement'])->name('mouvements.store');
    });


    // Gestion des types de transactions
    Route::resource('type-transactions', TypeTransactionController::class)->except(['edit']);

    // Route personnalisée pour edit
    Route::get('type-transactions/{id}/edit', [TypeTransactionController::class, 'edit'])
        ->name('type-transactions.edit');

    Route::put('/type_transactions/{type_transaction}', [TypeTransactionController::class, 'update'])->name('type_transactions.update');
    Route::delete('/type_transactions/{type_transaction}', [TypeTransactionController::class, 'destroy'])->name('type_transactions.destroy');
});
Route::get('grille_tarifaires/create', [GrilleTarifaireController::class, 'create'])->name('grille_tarifaires.create');


// Routes d'authentification
require __DIR__ . '/auth.php';

Route::get('/api/dashboard-data', [DashboardController::class, 'getDashboardData']);

Route::get('/mouvements/{id}/details', [CaisseController::class, 'getMouvementDetails'])->name('mouvements.details');

Route::middleware('auth')->group(function () {
    // Routes pour les caisses
    Route::resource('caisses', CaisseController::class);

    // Route pour créer un mouvement
    Route::get('/caisses/{caisse}/mouvements/create', [CaisseController::class, 'createMouvement'])
        ->name('caisses.mouvements.create');

    // Route pour enregistrer un mouvement
    Route::post('/caisses/mouvements', [CaisseController::class, 'storeMouvement'])
        ->name('caisses.mouvements.store');

    // Route API pour les opérations non remboursées
    Route::get('/api/caisses/{caisse}/operations-non-remboursees', [CaisseController::class, 'getOperationsNonRemboursees'])
        ->name('api.caisses.operations-non-remboursees');
    Route::patch('/produits/{id}/toggle-status', [ProduitController::class, 'toggleStatus'])
        ->name('produits.toggle-status');
    Route::resource('mouvements-produits', MouvementProduitController::class);
});

// Ajouter avec les autres routes de caisses
Route::patch('/caisses/{caisse}/toggle-status', [CaisseController::class, 'toggleStatus'])
    ->name('caisses.toggle-status');

Route::get('/produits/{id}/show', [ProduitController::class, 'show'])->name('produits.show');
