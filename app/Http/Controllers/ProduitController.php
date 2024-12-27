<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MouvementProduit;

class ProduitController extends Controller
{
    /**
     * Affiche une liste des produits avec pagination.
     *
     * @return \Illuminate\View\View
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $produits = Produit::query();

            return DataTables::of($produits)
                ->addColumn('balance', function ($produit) {
                    return number_format($produit->balance, 2, ',', ' ') . ' FCFA';
                })
                ->addColumn('status', function ($produit) {
                    return $produit->status
                        ? '<span class="badge bg-success">Actif</span>'
                        : '<span class="badge bg-danger">Inactif</span>';
                })
                ->addColumn('action', function ($produit) {
                    return view('produits.actions', compact('produit'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
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
        if (!auth()->user()->can('create-produits')) {
            return redirect()->route('produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer  un produit.');
        }

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
                'status' => 'nullable|boolean',
            ]);

            $validated['status'] = $request->has('status') ? filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN) : false;

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
        if (!auth()->user()->can('edit-produits')) {
            return redirect()->route('produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier un produit.');
        }

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
                'status' => 'nullable|boolean',
            ]);

            $validated['status'] = $request->has('status') ? filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN) : false;

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
        if (!auth()->user()->can('delete-produits')) {
            return redirect()->route('produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer le produit.');
        }

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

    public function toggleStatus(string $id)
    {
        try {
            $produit = Produit::findOrFail($id);
            $produit->status = !$produit->status;
            $produit->save();

            return redirect()->back()->with('success', 'Statut mis à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du statut');
        }
    }

    public function show($id)
    {
        $produit = Produit::findOrFail($id);
        $mouvements = MouvementProduit::where('produit_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produits.show', compact('produit', 'mouvements'));
    }
}
