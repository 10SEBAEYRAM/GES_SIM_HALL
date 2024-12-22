<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Produit;
use App\Models\TypeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Caisse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\GrilleTarifaire;

class TransactionController extends Controller
{
    public function index()
    {
        // Récupérer les transactions avec leurs relations
        $transactions = Transaction::with(['produit', 'typeTransaction', 'user'])->get();

        // Récupérer les produits actifs et les caisses
        $produits = Produit::where('actif', true)->get();
        $caisses = Caisse::all();

        // Récupérer les dates des transactions avec les totaux
        $transactionsDates = Transaction::selectRaw('DATE(created_at) as date, SUM(montant_trans) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Préparer les données pour le graphique
        $data = [
            'labels' => $transactionsDates->pluck('date'),   // Les dates
            'totals' => $transactionsDates->pluck('total'), // Les totaux
        ];

        // Retourner la vue avec toutes les données nécessaires
        return view('transactions.index', compact('transactions', 'produits', 'caisses', 'data'));
    }

    public function create()
    {
        $caisses = Caisse::all();
        $produits = Produit::where('actif', true)->get();
        $typeTransactions = TypeTransaction::all();

        // Passe les données à la vue
        return view('transactions.create', compact('caisses', 'produits', 'typeTransactions'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'type_transaction_id' => 'required|exists:type_transactions,id_type_transa',
                'produit_id' => 'required|exists:produits,id_prod',
                'montant' => 'required|numeric|min:0',
                // 'commission_grille_tarifaire' => 'required|numeric|min:0',  // Ajout de la validation de la commission
                'num_beneficiaire' => 'required|string|max:255',
                'frais_service' => 'numeric|nullable',
                'motif' => 'string|nullable|in:transfert,paiement_ceet,paiement_canal',
                'user_id' => 'required|exists:users,id_util',
                'id_caisse' => 'required|exists:caisses,id_caisse',
                'numero_compteur' => 'required_if:motif,paiement_ceet',
                'numero_validation' => 'required_if:motif,paiement_ceet',
                'numero_carte_paiement' => 'required_if:motif,paiement_canal',
           
             ]);

            // Récupérer la grille tarifaire correspondante
            try {
                // Convertir explicitement les valeurs
                $type_transaction_id = (int) $validated['type_transaction_id'];
                $produit_id = (int) $validated['produit_id'];
                $montant = (float) $validated['montant'];

                // Construction de la requête étape par étape
                $query = GrilleTarifaire::query();
                
                // Première étape : filtrer par type_transaction et produit
                $query->where('type_transaction_id', $type_transaction_id)
                      ->where('produit_id', $produit_id);
                
                // Debug de la première étape
                [
                    'Étape 1 - Paramètres convertis' => [
                        'type_transaction_id' => [
                            'valeur' => $type_transaction_id,
                            'type' => gettype($type_transaction_id)
                        ],
                        'produit_id' => [
                            'valeur' => $produit_id,
                            'type' => gettype($produit_id)
                        ],
                        'montant' => [
                            'valeur' => $montant,
                            'type' => gettype($montant)
                        ]
                    ],
                    'Étape 1 - Résultats initiaux' => $query->get()->map(function($item) {
                        return [
                            'id' => $item->id_grille_tarifaire,
                            'type_transaction_id' => [
                                'valeur' => $item->type_transaction_id,
                                'type' => gettype($item->type_transaction_id)
                            ],
                            'produit_id' => [
                                'valeur' => $item->produit_id,
                                'type' => gettype($item->produit_id)
                            ],
                            'montant_min' => [
                                'valeur' => $item->montant_min,
                                'type' => gettype($item->montant_min)
                            ],
                            'montant_max' => [
                                'valeur' => $item->montant_max,
                                'type' => gettype($item->montant_max)
                            ]
                        ];
                    })->toArray(),
                    'Étape 1 - SQL' => [
                        'requête' => $query->toSql(),
                        'paramètres' => $query->getBindings()
                    ]
                ];

                
// Deuxième étape : vérifier les montants
$montantFormatted = floatval($montant);

// Vérifions d'abord si une grille existe
$grilleTarifaire = $query
    ->where('montant_min', '<=', $montantFormatted)
    ->where('montant_max', '>=', $montantFormatted)
    ->first();

// Ajoutons un log pour voir les valeurs exactes
// dd('Tentative de recherche grille tarifaire', [
//     'montant' => $montantFormatted,
//     'type_transaction' => $validated['type_transaction_id'],
//     'produit' => $validated['produit_id']
// ]);
Log::info('SQL généré', [
    'sql' => $query->toSql(),
    'bindings' => $query->getBindings()
]);
Log::info('SQL généré', [
    'sql' => $query->toSql(),
    'bindings' => $query->getBindings()
]);

if (!$grilleTarifaire) {
    Log::warning('Aucune grille tarifaire trouvée pour:', [
        'type_transaction' => $validated['type_transaction_id'],
        'produit' => $validated['produit_id'],
        'montant' => $montant
    ]);
    return back()
        ->withInput()
        ->with('error', 'Aucune grille tarifaire trouvée pour ce montant et ce type de transaction.');
}
            } catch (\Exception $e) {
                Log::error('Erreur lors de la recherche de la grille tarifaire: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Erreur lors de la recherche de la grille tarifaire: ' . $e->getMessage());
            }

            // Récupérer la caisse, produit et type de transaction
            $caisse = Caisse::findOrFail($validated['id_caisse']);
            $produit = Produit::findOrFail($validated['produit_id']);
            $typeTransaction = TypeTransaction::findOrFail($validated['type_transaction_id']);
            $nomTransaction = $typeTransaction->nom_type_transa;

            // Les soldes avant
            $solde_caisse_avant = $caisse->balance_caisse;
            $solde_produit_avant = $produit->balance;

            // Calcul des soldes après avec la commission de la grille tarifaire
            $solde_produit_apres = $nomTransaction === "Dépôt"
                ? ($solde_produit_avant - $validated['montant'] + $grilleTarifaire->commission_grille_tarifaire)
                : ($solde_produit_avant + $validated['montant'] + $grilleTarifaire->commission_grille_tarifaire);

            // Gestion des frais de service
            $frais_service = $request->has('frais_service') ? $validated['frais_service'] : 0;

            // Calcul du solde caisse après
            $solde_caisse_apres = $nomTransaction === "Dépôt"
                ? ($solde_caisse_avant + $validated['montant'] + $frais_service)
                : ($solde_caisse_avant - $validated['montant']);

            // Vérification du solde
            if ($solde_caisse_apres < 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Solde insuffisant dans la caisse pour effectuer cette transaction.');
            }

            // Création de la transaction avec la commission de la grille tarifaire
            $transaction = Transaction::create([
                'type_transaction_id' => $validated['type_transaction_id'],
                'produit_id' => $validated['produit_id'],
                'user_id' => auth()->user()->id_util,
                'montant_trans' => $validated['montant'],
                'commission_grille_tarifaire' => $grilleTarifaire->commission_grille_tarifaire,
                'frais_service' => $frais_service,
                'num_beneficiaire' => $validated['num_beneficiaire'],
                'motif' => $request->input('motif', "pas de motif"),
                'statut' => 'COMPLETE',
                'solde_avant' => $solde_produit_avant,
                'solde_apres' => $solde_produit_apres,
                'solde_caisse_avant' => $solde_caisse_avant,
                'solde_caisse_apres' => $solde_caisse_apres,
                'id_caisse' => $validated['id_caisse'],
                'numero_compteur' => $request->input('numero_compteur'),
                'numero_validation' => $request->input('numero_validation'),
                'numero_carte_paiement' => $request->input('numero_carte_paiement'),
            ]);

            // Mise à jour des soldes
            $produit->update(['balance' => $solde_produit_apres]);
            $caisse->update(['balance_caisse' => $solde_caisse_apres]);

            DB::commit();

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction effectuée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la transaction : ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la transaction : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['produit', 'typeTransaction', 'user'])->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }

    public function getCommission(Request $request)
    {
        try {
            

            // Validate the request
            $validator = Validator::make($request->all(), [
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_trans' => 'required|numeric|min:0',
                'type_transaction_id' => 'required|exists:type_transactions,id_type_transa'
            ]);

            if ($validator->fails()) {
               
                return response()->json([
                    'success' => false,
                    'message' => 'Validation échouée',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find the product
            $produit = Produit::find($request->produit_id);
            if (!$produit) {
              
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }

            // Calculate commission
            try {
                $commission = $produit->getCommissionForAmount(
                    $request->montant_trans,
                    $request->type_transaction_id
                );

               
               $montant = $request->montant_trans;



                return response()->json([
                    'success' => true,
                    'commission' => $commission
                ]);
            } catch (\Exception $e) {
                Log::error('Commission calculation error:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('General error in getCommission:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            // Récupérer le produit et la caisse
            $produit = $transaction->produit;
            $caisse = Caisse::find($transaction->id_caisse);

            // Mise à jour de la balance du produit
            $this->updateProduitBalance($produit, $transaction);

            // Mise à jour de la balance de la caisse
            $this->updateCaisseBalance($caisse, $transaction);

            // Supprimer la transaction
            $transaction->delete();

            return redirect()->route('transactions.index')->with('success', 'Transaction supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('transactions.edit', compact('transaction'));
    }

    private function updateProduitBalance($produit, $transaction)
    {
        if ($transaction->typeTransaction->nom_type_transa == 'Dépôt') {
            $produit->balance += $transaction->montant_trans;
            $produit->balance -= $transaction->commission_grille_tarifaire;
        } elseif ($transaction->typeTransaction->nom_type_transa == 'Retrait') {
            $produit->balance -= $transaction->montant_trans;
            $produit->balance -= $transaction->commission_grille_tarifaire;
        }
        $produit->save();
    }

    private function updateCaisseBalance($caisse, $transaction)
    {
        if ($transaction->typeTransaction->nom_type_transa == 'Dépôt') {
            $caisse->balance_caisse -= $transaction->montant_trans;
            $caisse->balance_caisse -= $transaction->frais_service;
        } elseif ($transaction->typeTransaction->nom_type_transa == 'Retrait') {
            $caisse->balance_caisse -= $transaction->montant_trans;
            $caisse->balance_caisse -= $transaction->frais_service;
        }
        $caisse->save();
    }
    
}
