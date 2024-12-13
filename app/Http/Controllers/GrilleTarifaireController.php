<?php

namespace App\Http\Controllers;

use App\Models\GrilleTarifaire;
use App\Models\Produit;
use Illuminate\Http\Request;

class GrilleTarifaireController extends Controller
{
    public function index(Request $request)
    {
        $query = GrilleTarifaire::with('produit');


        if ($request->filled('produit_filter')) {
            $query->where('produit_id', $request->produit_filter);
        }


        if ($request->filled('montant_min_filter')) {
            $query->where('montant_min', '>=', $request->montant_min_filter);
        }


        if ($request->filled('montant_max_filter')) {
            $query->where('montant_max', '<=', $request->montant_max_filter);
        }


        if ($request->filled('commission_filter')) {
            $query->where('commission_grille_tarifaire', '<=', $request->commission_filter);
        }

        $grilleTarifaires = $query->paginate(10);
        $produits = Produit::where('actif', true)->get();

        return view('grille_tarifaires.index', compact('grilleTarifaires', 'produits'));
    }

    public function create()
    {
        if (!auth()->user()->can('create-grille_tarifaires')) {
            return redirect()->route('grille-tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette grille tarifaire.');
        }

        $produits = Produit::where('actif', true)->get();
        return view('grille_tarifaires.create', compact('produits'));
    }

    public function store(Request $request)
    {
        \Log::info('Données reçues :', $request->all());

        try {

            $validated = $request->validate([
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_min' => 'required|numeric|min:0',
                'montant_max' => 'required|numeric|gt:montant_min',
                'commission_grille_tarifaire' => 'required|numeric|min:0',
            ]);


            if ($validated['montant_max'] <= $validated['montant_min']) {
                return back()->withInput()->with('error', 'Le montant maximum doit être supérieur au montant minimum.');
            }

            // Vérifier si une grille tarifaire existe déjà pour cette plage
            $exists = GrilleTarifaire::where('produit_id', $validated['produit_id'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('montant_min', [$validated['montant_min'], $validated['montant_max']])
                        ->orWhereBetween('montant_max', [$validated['montant_min'], $validated['montant_max']]);
                })
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Une grille tarifaire existe déjà pour cette plage de montants.');
            }


            GrilleTarifaire::create($validated);

            return redirect()
                ->route('grille-tarifaires.index')
                ->with('success', 'Grille tarifaire créée avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la grille tarifaire : ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la grille tarifaire.');
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('edit-grille_tarifaires')) {
            return redirect()->route('grille-tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette grille tarifaire.');
        }
        $grilleTarifaire = GrilleTarifaire::findOrFail($id);
        $produits = Produit::where('actif', true)->get();
        return view('grille_tarifaires.edit', compact('grilleTarifaire', 'produits'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('create-grille_tarifaires')) {
            return redirect()->route('grille-tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer une grille tarifaire.');
        }
        try {
            $grilleTarifaire = GrilleTarifaire::findOrFail($id);

            $validated = $request->validate([
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_min' => 'required|numeric|min:0',
                'montant_max' => 'required|numeric|gt:montant_min',
                'commission_grille_tarifaire' => 'required|numeric|min:0',
            ]);


            $exists = GrilleTarifaire::where('produit_id', $validated['produit_id'])
                ->where('id_grille_tarifaire', '!=', $id)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('montant_min', [$validated['montant_min'], $validated['montant_max']])
                        ->orWhereBetween('montant_max', [$validated['montant_min'], $validated['montant_max']]);
                })
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Une grille tarifaire existe déjà pour cette plage de montants.');
            }

            $grilleTarifaire->update($validated);

            return redirect()
                ->route('grille-tarifaires.index')
                ->with('success', 'Grille tarifaire mise à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la grille tarifaire.');
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('delete-grille_tarifaires')) {
            return redirect()->route('grille_tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer une grille tarifaire.');
        }

        try {
            $grilleTarifaire = GrilleTarifaire::findOrFail($id);
            $grilleTarifaire->delete();

            return redirect()
                ->route('grille-tarifaires.index')
                ->with('success', 'Grille tarifaire supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('grille-tarifaires.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de la grille tarifaire.');
        }
    }
}
