<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class ProduitController extends Controller
{
    // Affiche la page des produits
    public function index()
    {
        return view('produits.index');
    }

    // Affiche les détails d'un produit
    public function show($id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    // Retourne les données des produits pour DataTables (méthode principale)
    public function getDatatable(Request $request)
    {
        try {
            // DB::connection()->getPdo(); // Test de connexion

            $query = Produit::select([
                'id_prod',
                'nom_prod',
                'balance',
                'actif'
            ])->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('nom_prod', function ($produit) {
                    return $produit->nom_prod;
                })
                ->addColumn('balance_formatted', function ($produit) {
                    return number_format($produit->balance, 2, ',', ' ') . ' FCFA';
                })
                ->addColumn('status_badge', function ($produit) {
                    return $produit->actif
                        ? '<span class="badge badge-success">Actif</span>'
                        : '<span class="badge badge-danger">Inactif</span>';
                })
                ->addColumn('actions', function ($produit) {
                    return view('produits.actions', compact('produit'))->render();
                })
                ->rawColumns(['status_badge', 'actions'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $query->where(function ($q) use ($request) {
                            $q->where('nom_prod', 'LIKE', "%{$request->search}%")
                                ->orWhere('balance', 'LIKE', "%{$request->search}%");
                        });
                    }
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Erreur DataTables Produits: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => 'Impossible de charger les données: ' . $e->getMessage()
            ], 500);
        }
    }


    // Retourne la vue pour créer un nouveau produit
    public function create()
    {
        return view('produits.create');
    }

    // Enregistre un nouveau produit
    public function store(Request $request)
    {
        $validated = $this->validateProduit($request);

        try {
            Produit::create($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de création de produit : ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    // Retourne la vue pour modifier un produit existant
    public function edit($id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    // Met à jour un produit existant
    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);
        $validated = $this->validateProduit($request, $id);

        try {
            $produit->update($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de mise à jour de produit : ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    // Supprime un produit
    public function destroy($id)
    {
        try {
            $produit = Produit::findOrFail($id);
            $produit->delete();

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de suppression de produit : ' . $e->getMessage());

            return redirect()
                ->route('produits.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du produit.');
        }
    }

    // Valide les données du produit
    private function validateProduit(Request $request, $id = null)
    {
        $uniqueRule = $id
            ? 'unique:produits,nom_prod,' . $id . ',id_prod'
            : 'unique:produits';

        return $request->validate([
            'nom_prod' => ['required', 'string', 'max:50', $uniqueRule],
            'balance' => 'required|numeric|min:0',
            'actif' => 'nullable|boolean',
        ], [
            'nom_prod.unique' => 'Ce nom de produit existe déjà.',
            'balance.numeric' => 'La balance doit être un nombre.',
            'balance.min' => 'La balance ne peut pas être négative.',
        ]);
    }
}
