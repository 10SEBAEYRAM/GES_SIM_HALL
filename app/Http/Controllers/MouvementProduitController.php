<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\MouvementProduit;

class MouvementProduitController extends Controller
{
    public function create()
    {
        // Récupérer tous les produits actifs
        $produits = Produit::where('actif', true)->get();

        return view('mouvements_produits.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id_prod',
            'description' => 'required|string',
            'volume_depot' => 'required|integer|min:0',
            'valeur_depot' => 'required|numeric|min:0',
            'commission_depot' => 'required|numeric|min:0',
            'volume_retrait' => 'required|integer|min:0',
            'valeur_retrait' => 'required|numeric|min:0',
            'commission_retrait' => 'required|numeric|min:0',
            'montant_ht' => 'required|numeric|min:0',
            'retenue' => 'required|numeric|min:0',
            'montant_net' => 'required|numeric|min:0',
            'commission_produit' => 'required|numeric|min:0'
        ]);

        try {
            $produit = Produit::findOrFail($validated['produit_id']);

            // Création du mouvement avec les données du formulaire
            $mouvement = MouvementProduit::create([
                'produit_id' => $validated['produit_id'],
                'type_mouvement' => 'CREDIT',
                'description' => $validated['description'],
                'volume_depot' => $validated['volume_depot'],
                'valeur_depot' => $validated['valeur_depot'],
                'commission_depot' => $validated['commission_depot'],
                'volume_retrait' => $validated['volume_retrait'],
                'valeur_retrait' => $validated['valeur_retrait'],
                'commission_retrait' => $validated['commission_retrait'],
                'montant_ht' => $validated['montant_ht'],
                'retenue' => $validated['retenue'],
                'montant_net' => $validated['montant_net'],
                'commission_produit' => $validated['commission_produit']
            ]);

            // Mise à jour de la balance du produit avec la commission totale
            $produit->balance += $validated['commission_produit'];
            $produit->save();

            return redirect()
                ->route('produits.index')
                ->with('success', 'Commission de ' . number_format($validated['commission_produit'], 0, ',', ' ') . ' FCFA ajoutée avec succès');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout de la commission : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $mouvement = MouvementProduit::with('produit')->findOrFail($id);
        return view('mouvements_produits.show', compact('mouvement'));
    }
}
