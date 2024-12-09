<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProduitController extends Controller
{
    /**
     * Affiche une liste des produits avec pagination.
     *
     * @return \Illuminate\View\View
     */
    public function data(Request $request)
    {
        // Vérification de la requête Ajax
        if ($request->ajax()) {
            // Récupération de tous les produits avec une requête
            $produits = Produit::query();

            // Utilisation de DataTables
            return DataTables::of($produits)
                // Formatage de la colonne balance
                ->addColumn('balance', function ($produit) {
                    return number_format($produit->balance, 2, ',', ' ') . ' FCFA';
                })

                // Colonne de statut avec du HTML conditionnel
                ->addColumn('status', function ($produit) {
                    return $produit->actif
                        ? '<span class="badge bg-success">Actif</span>'
                        : '<span class="badge bg-danger">Inactif</span>';
                })

                // Colonne d'actions avec boutons Modifier et Supprimer
                ->addColumn('action', function ($produit) {
                    return view('produits.actions', compact('produit'))->render();
                })

                // Autoriser le rendu de HTML brut
                ->rawColumns(['status', 'action'])

                // Générer la réponse DataTables
                ->make(true);
        }

        // Optionnel : gérer le cas où ce n'est pas une requête Ajax
        return response()->json(['error' => 'Requête non autorisée'], 403);
    }




    public function index()
    {
        $produits = Produit::paginate(10);
        return view('produits.index', compact('produits'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau produit.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
     * Enregistre un nouveau produit dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom_prod' => 'required|string|max:50|unique:produits',
                'balance' => 'required|numeric|min:0',
                'actif' => 'nullable|boolean',
            ]);

            $validated['actif'] = $request->has('actif') ? filter_var($request->input('actif'), FILTER_VALIDATE_BOOLEAN) : false;

            Produit::create($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'édition pour un produit spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    /**
     * Met à jour un produit existant dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            $produit = Produit::findOrFail($id);

            $validated = $request->validate([
                'nom_prod' => 'required|string|max:50|unique:produits,nom_prod,' . $id . ',id_prod',
                'balance' => 'required|numeric|min:0',
                'actif' => 'nullable|boolean',
            ]);

            $validated['actif'] = $request->has('actif') ? filter_var($request->input('actif'), FILTER_VALIDATE_BOOLEAN) : false;

            $produit->update($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }




    /**
     * Supprime un produit spécifique de la base de données.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        try {
            $produit = Produit::findOrFail($id);
            $produit->delete();

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('produits.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du produit : ' . $e->getMessage());
        }
    }
}
