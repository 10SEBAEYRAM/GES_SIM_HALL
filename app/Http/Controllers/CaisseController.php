<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    // Afficher toutes les caisses
    public function index()
    {
        $caisses = Caisse::paginate(10); // Récupère toutes les caisses
        return view('caisses.index', compact('caisses'));
    }

    // Afficher le formulaire de création d'une caisse
    public function create()
    {
        return view('caisses.create');
    }

    // Enregistrer une nouvelle caisse
    public function store(Request $request)
    {
        $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'balance_caisse' => 'required|numeric',
        ]);

        Caisse::create($request->all()); // Crée une nouvelle caisse
        return redirect()->route('caisses.index')->with('success', 'Caisse créée avec succès');
    }

    // Afficher le formulaire d'édition d'une caisse
    public function edit($id_caisse)
    {
        $caisse = Caisse::findOrFail($id_caisse); // Trouver la caisse par son ID
        return view('caisses.edit', compact('caisse'));
    }
    

    // Mettre à jour une caisse existante
    public function update(Request $request, $id_caisse)
{
    $caisse = Caisse::findOrFail($id_caisse); // Trouver la caisse par son ID

    // Validation des données
    $validated = $request->validate([
        'nom_caisse' => 'required|string|max:255',
        'balance_caisse' => 'required|numeric',
    ]);

    // Mettre à jour les données de la caisse
    $caisse->update($validated);

    // Rediriger avec un message de succès
    return redirect()->route('caisses.index')->with('success', 'Caisse mise à jour avec succès.');
}


    // Supprimer une caisse
    public function destroy(Caisse $caisse)
    {
        $caisse->delete(); // Supprime la caisse
        return redirect()->route('caisses.index')->with('success', 'Caisse supprimée avec succès');
    }
}
