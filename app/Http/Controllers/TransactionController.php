<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Produit;
use App\Models\TypeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['produit', 'typeTransaction', 'user'])->get();
        $produits = Produit::where('actif', true)->get();
        return view('transactions.index', compact('transactions', 'produits'));
    }

    public function create()
    {
        $produits = Produit::where('actif', true)->get();
        $typeTransactions = TypeTransaction::all();
        return view('transactions.create', compact('produits', 'typeTransactions'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'type_transaction_id' => 'required|exists:type_transactions,id_type_transa',
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_trans' => 'required|numeric|min:0',
                'num_beneficiaire' => 'required|string|max:50',
            ]);

            $produit = Produit::findOrFail($validated['produit_id']);
            $typeTransaction = TypeTransaction::findOrFail($validated['type_transaction_id']);

            // Calcul de la commission
            $commission = $produit->getCommissionForAmount($validated['montant_trans']);

            // Calcul des soldes
            $soldes = Transaction::calculerSoldes(
                $validated['produit_id'], 
                $validated['montant_trans'],
                $typeTransaction->nom_type_transa
            );


            // Vérification du solde suffisant pour les retraits
            if ($typeTransaction->nom_type_transa === 'Retrait' && $soldes['solde_apres'] < 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Solde insuffisant pour effectuer cette transaction.');
            }

            // utilisateur connecté
            $user = auth()->user()->id_util;


            $transaction = Transaction::create([
                'type_transaction_id' => $validated['type_transaction_id'],
                'produit_id' => $validated['produit_id'],
                'user_id' => $user,
                'montant_trans' => $validated['montant_trans'],
                'commission_appliquee' => $commission,
                'num_beneficiaire' => $validated['num_beneficiaire'],
                'statut' => 'COMPLETE',
                'solde_avant' => $soldes['solde_avant'],
                'solde_apres' => $soldes['solde_apres'],
                'solde_caisse_avant' => 0, // À implémenter selon votre logique de caisse
                'solde_caisse_apres' => 0  // À implémenter selon votre logique de caisse
            ]);


            // Mise à jour du solde du produit
            $produit->updateBalance(
                $validated['montant_trans'],
                $typeTransaction->nom_type_transa
            );

            DB::commit();

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction effectuée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
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
                'montant' => 'required|numeric|min:0',
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
}