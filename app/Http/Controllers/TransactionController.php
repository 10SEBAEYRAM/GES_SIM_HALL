<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Produit;
use App\Models\TypeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Caisse;
use Illuminate\Support\Facades\Log;




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

            // Validation des données
            $validated = $request->validate([
                'type_transaction_id' => 'required|exists:type_transactions,id_type_transa',
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_trans' => 'required|numeric|min:0',
                'num_beneficiaire' => 'required|string|max:255',
                'frais_service' => 'numeric|nullable',
                'motif' => 'string|nullable|in:transfert,paiement_ceet,paiement_canal',
                'user_id' => 'required|exists:users,id_util',
                'id_caisse' => 'required|exists:caisses,id_caisse',
            ]);


            // Récupérer la caisse, produit et type de transaction

            $caisse = Caisse::findOrFail($validated['id_caisse']);

            $produit = Produit::findOrFail($validated['produit_id']);

            $typeTransaction = TypeTransaction::findOrFail($validated['type_transaction_id']);

            $nomTransaction = $typeTransaction->nom_type_transa;
            // dd($nomTransaction);
            $commission = $produit->getCommissionForAmount($validated['montant_trans']);

            $solde_caisse_avant = $caisse->balance_caisse;

            $solde_produit_avant = $produit->balance;
            // dd($solde_produit_avant);
            $solde_produit_apres = $nomTransaction === "Dépôt"
                ? ($solde_produit_avant - $validated['montant_trans'] + $commission)
                : ($solde_produit_avant + $validated['montant_trans'] + $commission);
            // dd($solde_produit_apres);



            // Calcul du solde caisse après avec gestion de frais_service null
            $frais_service = $request->has('frais_service') ? $validated['frais_service'] : 0;

            $solde_caisse_apres = $nomTransaction === "Dépôt"
                ? ($solde_caisse_avant + $validated['montant_trans'] + $frais_service)
                : ($solde_caisse_avant - $validated['montant_trans']);
            // dd($solde_caisse_apres);

            // Vérification du solde
            if ($solde_caisse_apres < 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Solde insuffisant dans la caisse pour effectuer cette transaction.');
            }

            // Enregistrement de la transaction
            $transaction = Transaction::create([
                'type_transaction_id' => $validated['type_transaction_id'],
                'produit_id' => $validated['produit_id'],
                'user_id' => auth()->user()->id_util,
                'montant_trans' => $validated['montant_trans'],
                'commission_appliquee' => $commission,
                'frais_service' => $frais_service,
                'num_beneficiaire' => $validated['num_beneficiaire'],
                'motif' => $request->input('motif', "pas de motif"),
                'statut' => 'COMPLETE',
                'solde_avant' => $solde_produit_avant,
                'solde_apres' => $solde_produit_apres,
                'solde_caisse_avant' => $solde_caisse_avant,
                'solde_caisse_apres' => $solde_caisse_apres,
                'id_caisse' => $validated['id_caisse'],
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
                ->with('error', 'Une erreur est survenue lors de la création de la transaction.');
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
            $request->validate([
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_trans' => 'required|numeric|min:0',
            ]);

            $produit = Produit::findOrFail($request->produit_id);
            $commission = $produit->getCommissionForAmount($request->montant);

            return response()->json([
                'success' => true,
                'commission' => $commission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de la commission',
                'error' => $e->getMessage()
            ], 400);
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
            $produit->balance -= $transaction->commission_appliquee;
        } elseif ($transaction->typeTransaction->nom_type_transa == 'Retrait') {
            $produit->balance -= $transaction->montant_trans;
            $produit->balance -= $transaction->commission_appliquee;
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
