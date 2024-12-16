<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Caisse;
use Illuminate\Http\Request;

class CaisseController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Caisse::query();

            // Appliquer les filtres dynamiquement
            if ($request->filled('date_debut')) {
                $query->whereDate('created_at', '>=', $request->date_debut);
            }

            if ($request->filled('date_fin')) {
                $query->whereDate('created_at', '<=', $request->date_fin);
            }

            if ($request->filled('montant_min')) {
                $query->where('balance_caisse', '>=', $request->montant_min);
            }

            if ($request->filled('montant_max')) {
                $query->where('balance_caisse', '<=', $request->montant_max);
            }

            return DataTables::of($query)

                ->addColumn('actions', function ($caisse) {
                    return '
                    <div class="flex space-x-2">
                        <a href="' . route('caisses.edit', $caisse->id_caisse) . '" class="text-blue-600 hover:text-blue-800">Modifier</a>
                        <button onclick="confirmDelete(' . $caisse->id_caisse . ')" class="text-red-600 hover:text-red-800">Supprimer</button>
                    </div>
                ';
                })
                ->editColumn('balance_caisse', function ($caisse) {
                    return number_format($caisse->balance_caisse, 2, ',', ' ') . ' XOF';
                })
                ->editColumn('created_at', function ($caisse) {
                    return $caisse->created_at->format('d/m/Y H:i');
                })
                ->editColumn('updated_at', function ($caisse) {
                    return $caisse->updated_at->format('d/m/Y H:i');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }


        $caisses = Caisse::paginate(10);
        $total_balance = Caisse::sum('balance_caisse');

        return view('caisses.index', compact('caisses', 'total_balance'));
    }


    public function create()
    {
        if (!auth()->user()->can('create-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer une caisse.');
        }

        // Déterminer le type de création (caisse ou mouvement)
        $type = request('type');

        if ($type === 'mouvement') {
            $caisses = Caisse::all();
            return view('caisses.caisse_transactions.create', [
                'caisses' => $caisses,
                'formFields' => [
                    'type_mouvement' => [
                        'emprunt' => 'Emprunt',
                        'remboursement' => 'Remboursement',
                        'retrait' => 'Retrait'
                    ]
                ]
            ]);
        }

        // Vue par défaut pour la création d'une caisse
        return view('caisses.create');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Si c'est un mouvement de caisse
            if ($request->has('type_mouvement')) {
                $validated = $request->validate([
                    'type_mouvement' => 'required|in:emprunt,remboursement,retrait',
                    'id_caisse' => 'required|exists:caisses,id_caisse',
                    'montant' => 'required|numeric|min:0',
                    'motif' => 'required|string'
                ]);

                $caisse = Caisse::findOrFail($validated['id_caisse']);
                $montant = $validated['montant'];

                // Vérification du solde pour les retraits et emprunts
                if (in_array($validated['type_mouvement'], ['retrait', 'emprunt'])) {
                    if ($caisse->balance_caisse < $montant) {
                        throw new \Exception('Solde insuffisant dans la caisse.');
                    }
                }

                // Mise à jour des colonnes selon le type de mouvement
                switch ($validated['type_mouvement']) {
                    case 'emprunt':
                        $caisse->emprunt_sim_hall += $montant;
                        $caisse->balance_caisse -= $montant;
                        break;
                    case 'remboursement':
                        if ($caisse->emprunt_sim_hall < $montant) {
                            throw new \Exception('Le montant du remboursement ne peut pas être supérieur au montant emprunté.');
                        }
                        $caisse->remboursement_sim_hall += $montant;
                        $caisse->emprunt_sim_hall -= $montant;
                        $caisse->balance_caisse += $montant;
                        break;
                    case 'retrait':
                        $caisse->montant_retrait += $montant;
                        $caisse->balance_caisse -= $montant;
                        break;
                }

                $caisse->save();

                DB::commit();

                return redirect()
                    ->route('caisses.index')
                    ->with('success', 'Mouvement de caisse enregistré avec succès');
            }

            // Sinon, c'est une création de caisse normale
            $request->validate([
                'nom_caisse' => 'required|string|max:255',
                'balance_caisse' => 'required|numeric',
            ]);

            Caisse::create($request->all());

            DB::commit();

            return redirect()->route('caisses.index')
                ->with('success', 'Caisse créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        if (!auth()->user()->can('edit-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier une caisse.');
        }
        $caisse = Caisse::findOrFail($id);
        return view('caisses.edit', compact('caisse'));
    }


    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('edit-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier une caisse.');
        }

        $caisse = Caisse::findOrFail($id);

        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'balance_caisse' => 'required|numeric',
        ]);

        $caisse->update($validated);

        return redirect()->route('caisses.index')->with('success', 'Caisse mise à jour avec succès.');
    }

    public function Destroy($id)
    {

        if (!auth()->user()->can('delete-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer une caisse.');
        }

        $caisse = Caisse::findOrFail($id);

        try {

            $caisse->delete();


            return redirect()->route('caisses.index')
                ->with('success', 'Caisse supprimée avec succès');
        } catch (\Exception $e) {

            return redirect()->route('caisses.index')
                ->with('error', 'Erreur lors de la suppression');
        }
    }
    public function empruntSimHall(Request $request)
    {
        // Récupérer la caisse
        $caisse = Caisse::first();

        // Ajouter le montant de l'emprunt à la caisse
        $caisse->update([
            'solde_balance' => $caisse->solde_balance + $request->montant,
            'emprunt_sim_hall' => $caisse->emprunt_sim_hall + $request->montant,
        ]);

        return back()->with('success', 'Emprunt ajouté à la Caisse Sim Hall.');
    }

    public function retraitCaisse(Request $request)
    {
        // Récupérer la caisse
        $caisse = Caisse::first();

        // Vérifier que la caisse a suffisamment de fonds
        if ($caisse->solde_balance < $request->montant) {
            return back()->with('error', 'Fonds insuffisants dans la caisse.');
        }

        // Effectuer le retrait et mettre à jour la caisse
        $caisse->update([
            'solde_balance' => $caisse->solde_balance - $request->montant,
            'montant_retrait' => $caisse->montant_retrait + $request->montant,
        ]);

        return back()->with('success', 'Retrait effectué avec succès.');
    }

    public function remboursementSimHall(Request $request)
    {
        // Récupérer la caisse
        $caisse = Caisse::first();

        // Vérifier que l'emprunt à rembourser est supérieur à 0
        if ($caisse->emprunt_sim_hall < $request->montant) {
            return back()->with('error', 'Le montant du remboursement dépasse le montant de l\'emprunt.');
        }

        // Effectuer le remboursement et mettre à jour la caisse
        $caisse->update([
            'solde_balance' => $caisse->solde_balance - $request->montant,
            'emprunt_sim_hall' => $caisse->emprunt_sim_hall - $request->montant,
            'remboursement_sim_hall' => $caisse->remboursement_sim_hall + $request->montant,
        ]);

        return back()->with('success', 'Remboursement effectué à Sim Hall.');
    }
}
