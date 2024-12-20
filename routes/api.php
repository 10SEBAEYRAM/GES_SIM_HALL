<?php

use App\Models\GrilleTarifaire;
use App\Http\Controllers\GrilleTarifaireController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Route pour le calcul de la commission
    Route::get('/grille-tarifaires', [GrilleTarifaireController::class, 'commissionGrilleTarifaire']);
    
    // Route de test
    Route::get('/grille-tarifaires/test', function () {
        $grilles = GrilleTarifaire::with(['typeTransaction', 'produit'])->get();
        return response()->json([
            'grilles' => $grilles->map(function ($grille) {
                return [
                    'id' => $grille->id_grille_tarifaire,
                    'type_transaction' => [
                        'id' => $grille->type_transaction_id,
                        'nom' => $grille->typeTransaction->nom_type_transa
                    ],
                    'produit' => [
                        'id' => $grille->produit_id,
                        'nom' => $grille->produit->nom_prod
                    ],
                    'montant_min' => $grille->montant_min,
                    'montant_max' => $grille->montant_max,
                    'commission' => $grille->commission_grille_tarifaire
                ];
            })
        ]);
    });
});