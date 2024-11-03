<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produits = Produit::paginate(10);
        return view('produits.index', compact('produits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom_prod' => 'required|string|max:50|unique:produits',
                'balance' => 'required|numeric|min:0',
                'actif' => 'boolean',
            ]);

            $validated['actif'] = (bool)$request->input('actif', false);

            Produit::create($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du produit.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $produit = Produit::findOrFail($id);

            $validated = $request->validate([
                'nom_prod' => 'required|string|max:50|unique:produits,nom_prod,' . $id . ',id_prod',
                'balance' => 'required|numeric|min:0',
                'actif' => 'boolean',
            ]);

            $validated['actif'] = (bool)$request->input('actif', false);

            $produit->update($validated);

            return redirect()
                ->route('produits.index')
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du produit.');
        }
    }

    /**
     * Remove the specified resource from storage.
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
                ->with('error', 'Une erreur est survenue lors de la suppression du produit.');
        }
    }
}
