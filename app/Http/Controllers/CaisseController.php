<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Caisse;

use App\Models\MouvementCaisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



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


        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255|unique:caisses,nom_caisse',
            'balance_caisse' => 'required|numeric|min:0',
        ], [
            'nom_caisse.required' => 'Le nom de la caisse est requis',
            'nom_caisse.unique' => 'Ce nom de caisse existe déjà',
            'balance_caisse.required' => 'La balance initiale est requise',
            'balance_caisse.numeric' => 'La balance doit être un nombre',
            'balance_caisse.min' => 'La balance ne peut pas être négative',
        ]);

        try {
            $caisse = Caisse::create($validated);

            return redirect()
                ->route('caisses.index')
                ->with('success', 'Caisse créée avec succès');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la caisse : ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (!auth()->user()->can('edit-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier une caisse.');
        }
        // Récupérer la caisse
        $caisse = Caisse::findOrFail($id);

        return view('caisses.edit', compact('caisse'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('update-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à mettre à jour une caisse.');
        }
        // Validation
        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'balance_caisse' => 'required|numeric|min:0',
        ]);

        // Récupérer la caisse
        $caisse = Caisse::findOrFail($id);

        // Mettre à jour la caisse
        $caisse->update($validated);

        return redirect()
            ->route('caisses.index')
            ->with('success', 'Caisse mise à jour avec succès');
    }

    public function createMouvement()
    {
        if (!auth()->user()->can('create-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer un mouvement.');
        }

        $caisses = Caisse::all();
        return view('caisses.mouvements.create', compact('caisses'));
    }

    public function storeMouvement(Request $request)
    {
        try {
            // DD 1: Voir les données initiales
            [
                'Données initiales' => [
                    'Request' => $request->all(),
                    'User' => auth()->user(),
                    'User ID' => auth()->user()->id_util // Assurez-vous d'utiliser la bonne colonne ID
                ]
            ];

            DB::beginTransaction();

            // Validation
            $validated = $request->validate([
                'id_caisse' => 'required|exists:caisses,id_caisse',
                'type_mouvement' => 'required|in:emprunt,remboursement,retrait',
                'montant' => 'required|numeric|min:0',
                'motif' => 'required|string'
            ]);

            // DD 2: Après validation
            [
                'Données validées' => $validated
            ];

            // Récupération de la caisse
            $caisse = Caisse::findOrFail($validated['id_caisse']);
            $montant = (float)$validated['montant'];
            $soldeAvant = (float)$caisse->balance_caisse;

            // DD 3: Après récupération de la caisse
            [
                'Données caisse' => [
                    'Caisse' => $caisse->toArray(),
                    'Montant' => $montant,
                    'Solde avant' => $soldeAvant
                ]
            ];

            // Récupération de l'ID utilisateur correct
            $userId = auth()->user()->id_util;

            // Traitement selon le type de mouvement
            switch ($validated['type_mouvement']) {
                case 'emprunt':
                    if ($montant > $soldeAvant) {
                        throw new \Exception('Solde insuffisant pour cet emprunt');
                    }
                    $caisse->balance_caisse = $soldeAvant - $montant;
                    $caisse->total_emprunts = (float)($caisse->total_emprunts ?? 0) + $montant;
                    break;

                case 'remboursement':
                    $caisse->balance_caisse = $soldeAvant + $montant;
                    $caisse->total_remboursements = (float)($caisse->total_remboursements ?? 0) + $montant;
                    break;

                case 'retrait':
                    if ($montant > $soldeAvant) {
                        throw new \Exception('Solde insuffisant pour ce retrait');
                    }
                    $caisse->balance_caisse = $soldeAvant - $montant;
                    $caisse->total_retraits = (float)($caisse->total_retraits ?? 0) + $montant;
                    break;
            }

            // DD 4: Après calculs
            [
                'Après calculs' => [
                    'Type mouvement' => $validated['type_mouvement'],
                    'Solde avant' => $soldeAvant,
                    'Montant' => $montant,
                    'Nouveau solde' => $caisse->balance_caisse,
                    'Nouveaux totaux' => [
                        'Emprunts' => $caisse->total_emprunts,
                        'Remboursements' => $caisse->total_remboursements,
                        'Retraits' => $caisse->total_retraits
                    ]
                ]
            ];

            // Sauvegarde de la caisse
            $caisse->save();

            // Création du mouvement avec l'ID utilisateur correct
            $mouvement = MouvementCaisse::create([
                'caisse_id' => $caisse->id_caisse,
                'type_mouvement' => $validated['type_mouvement'],
                'montant' => $montant,
                'motif' => $validated['motif'],
                'solde_avant' => $soldeAvant,
                'solde_apres' => $caisse->balance_caisse,
                'user_id' => $userId
            ]);

            // DD 5: Après création du mouvement
            // Si le `dd` est au milieu du code
            [
                'Mouvement créé' => [
                    'Mouvement' => $mouvement->toArray(),
                    'Caisse mise à jour' => $caisse->fresh()->toArray()
                ]
            ];

            // Ou simplement supprimer la partie complète si elle n'est pas utilisée

            DB::commit();

            return redirect()
                ->route('caisses.index')
                ->with('success', 'Mouvement enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur dans storeMouvement', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // DD en cas d'erreur
            [
                'Erreur' => [
                    'Message' => $e->getMessage(),
                    'Trace' => $e->getTrace(),
                    'Request' => $request->all()
                ]
            ];

            return back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
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
            'balance_caisse' => $caisse->balance_caisse + $request->montant,
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
            'balance_caisse' => $caisse->balance_caisse - $request->montant,
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
            'balance_caisse' => $caisse->balance_caisse - $request->montant,
            'emprunt_sim_hall' => $caisse->emprunt_sim_hall - $request->montant,
            'remboursement_sim_hall' => $caisse->remboursement_sim_hall + $request->montant,
        ]);

        return back()->with('success', 'Remboursement effectué à Sim Hall.');
    }

    public function show($id)
    {
        // Récupérer la caisse avec ses mouvements
        $caisse = Caisse::with(['mouvements' => function ($query) {
            $query->with('user')
                ->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        // Pour déboguer, ajoutez temporairement ce dd()
        [
            'caisse' => $caisse->toArray(),
            'nom_caisse' => $caisse->nom_caisse,
            'balance' => $caisse->balance_caisse,
            'created_at' => $caisse->created_at,
            'mouvements' => $caisse->mouvements->toArray()
        ];

        return view('caisses.show', [
            'caisse' => $caisse,
            'mouvements' => $caisse->mouvements
        ]);
    }
}
