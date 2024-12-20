<?php

namespace App\Http\Controllers;

use App\Models\GrilleTarifaire;
use App\Models\TypeTransaction;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;  // Ajout de cet import


class GrilleTarifaireController extends Controller
{
    public function index(Request $request)
{
    // Récupérer les données nécessaires
    $typeTransactions = TypeTransaction::all();
    $produits = Produit::where('actif', true)->get();

    // Construction de la requête de base
    $query = GrilleTarifaire::with(['typeTransaction', 'produit']);

    // Appliquer les filtres si présents
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

    // Exécuter la requête
    $grilleTarifaires = $query->get();

    // Retourner la vue avec toutes les données nécessaires
    return view('grille_tarifaires.index', compact('grilleTarifaires', 'typeTransactions', 'produits'));
}
    

    public function create()
    {
        if (!auth()->user()->can('create-grille_tarifaires')) {
            return redirect()->route('grille-tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette grille tarifaire.');
        }
    
        $produits = Produit::where('actif', true)->get();
        $typeTransactions = TypeTransaction::all(); // Récupération des types de transactions
        
        return view('grille_tarifaires.create', compact('typeTransactions', 'produits'));
    }
    public function getData()
    {
        $grilleTarifaires = GrilleTarifaire::with(['typeTransaction', 'produit']);
    
        return DataTables::of($grilleTarifaires)
            ->addColumn('actions', function ($grille) {
                // Assurez-vous d'avoir une vue 'grille_tarifaires.actions' pour les actions (Modifier/Supprimer)
                return view('grille_tarifaires.actions', compact('grille'))->render();
            })
            ->editColumn('montant_min', function ($grille) {
                return number_format($grille->montant_min, 0, ',', ' ') . ' FCFA';
            })
            ->editColumn('montant_max', function ($grille) {
                return number_format($grille->montant_max, 0, ',', ' ') . ' FCFA';
            })
            ->editColumn('commission_grille_tarifaire', function ($grille) {
                return number_format($grille->commission_grille_tarifaire, 0, ',', ' ') . ' FCFA';
            })
            ->rawColumns(['actions']) // Indique que la colonne 'actions' contient du HTML
            ->make(true);
    }
    

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type_transaction_id' => 'required|exists:type_transactions,id_type_transa',
                'produit_id' => 'required|exists:produits,id_prod',
                'montant_min' => 'required|numeric|min:0',
                'montant_max' => 'required|numeric|gt:montant_min',
                'commission_grille_tarifaire' => 'required|numeric|min:0',
            ], [
                'type_transaction_id.required' => 'Le type de transaction est obligatoire',
                'type_transaction_id.exists' => 'Le type de transaction sélectionné n\'existe pas',
                'produit_id.required' => 'Le produit est obligatoire',
                'produit_id.exists' => 'Le produit sélectionné n\'existe pas',
                'montant_min.required' => 'Le montant minimum est obligatoire',
                'montant_min.numeric' => 'Le montant minimum doit être un nombre',
                'montant_min.min' => 'Le montant minimum doit être supérieur ou égal à 0',
                'montant_max.required' => 'Le montant maximum est obligatoire',
                'montant_max.numeric' => 'Le montant maximum doit être un nombre',
                'montant_max.gt' => 'Le montant maximum doit être supérieur au montant minimum',
                'commission_grille_tarifaire.required' => 'La commission est obligatoire',
                'commission_grille_tarifaire.numeric' => 'La commission doit être un nombre',
                'commission_grille_tarifaire.min' => 'La commission doit être supérieure ou égale à 0'
            ]);

            if ($validated['montant_max'] <= $validated['montant_min']) {
                return back()->withInput()->with('error', 'Le montant maximum doit être supérieur au montant minimum.');
            }

            // Vérifier si une grille tarifaire existe déjà pour cette plage et ce type de transaction
            $exists = GrilleTarifaire::where('produit_id', $validated['produit_id'])
                ->where('type_transaction_id', $validated['type_transaction_id'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('montant_min', [$validated['montant_min'], $validated['montant_max']])
                        ->orWhereBetween('montant_max', [$validated['montant_min'], $validated['montant_max']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('montant_min', '<=', $validated['montant_min'])
                              ->where('montant_max', '>=', $validated['montant_max']);
                        });
                })
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Une grille tarifaire existe déjà pour cette plage de montants et ce type de transaction.');
            }

            GrilleTarifaire::create($validated);

            return redirect()
                ->route('grille-tarifaires.index')
                ->with('success', 'Grille tarifaire créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la grille tarifaire : ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la grille tarifaire.');
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('edit-grille_tarifaires')) {
            return redirect()->route('grille-tarifaires.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer une grille tarifaire.');
        }
        $grilleTarifaire = GrilleTarifaire::findOrFail($id);
        $typeTransactions = TypeTransaction::all(); // Récupérer les types de transactions

        $produits = Produit::where('actif', true)->get();
    
        return view('grille_tarifaires.edit', compact('grilleTarifaire', 'typeTransactions', 'produits'));
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