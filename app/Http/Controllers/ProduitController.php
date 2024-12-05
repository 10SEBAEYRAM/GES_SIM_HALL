<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class ProduitController extends Controller
{
    /**
     * Affiche la page d'index des produits.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('produits.index');
    }

    /**
     * Récupère les données pour DataTables avec filtrage avancé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    public function getProduits(Request $request)
    {
        if ($request->ajax()) {
            $produits = Produit::all();  // Ou un autre filtre selon vos besoins
            dd($produits);
            return DataTables::of($produits)
                ->make(true);
        }
    }
    public function getProduitsData()
    {
        $produits = Produit::select('id_prod', 'nom_prod', 'balance', 'actif')
            ->get(); // Vous pouvez ajuster cette requête selon vos besoins

        return datatables()->of($produits)
            ->addColumn('action', function ($row) {
                return view('produits.actions', compact('row'))->render(); // Assurez-vous que l'action est bien configurée
            })
            ->make(true);
    }

    public function getDatatable(Request $request)
    {
        try {

            $query = Produit::query();

            return DataTables::of($query)
                ->addColumn('balance', function ($produit) {
                    return number_format($produit->balance, 2, ',', ' ') . ' FCFA';
                })
                ->addColumn('status', function ($produit) {
                    return $produit->actif;
                })
                ->addColumn('action', function ($produit) {
                    return $produit->id_prod;
                })
                ->addColumn('nom_prod', function ($produit) {
                    return $produit->nom_prod;
                })

                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $searchValue = $request->input('search')['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('nom_prod', 'like', "%{$searchValue}%")
                                ->orWhere('balance', 'like', "%{$searchValue}%");
                        });
                    }
                    return $query;
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Erreur DataTables : ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur est survenue lors du chargement des données',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Applique les filtres par colonnes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function applyColumnFilters($query, $request)
    {
        if ($request->has('columns')) {
            foreach ($request->input('columns') as $column) {
                if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                    $columnName = $column['data'];
                    $searchValue = $column['search']['value'];

                    switch ($columnName) {
                        case 'nom_prod':
                            $query->where('nom_prod', 'like', "%{$searchValue}%");
                            break;
                        case 'balance':
                            $query->where('balance', 'like', "%{$searchValue}%");
                            break;
                        case 'actif':
                            $query->where('actif', $searchValue);
                            break;
                    }
                }
            }
        }
    }

    /**
    
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
   
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
   
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

    /**
     * Valide les données du produit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $id
     * @return array
     */
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
            Log::error('Erreur de suppression de produit : ' . $e->getMessage());

            return redirect()
                ->route('produits.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du produit.');
        }
    }
}
