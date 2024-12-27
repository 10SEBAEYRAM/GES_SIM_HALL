<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Caisse;

use App\Models\MouvementCaisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



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

    public function createMouvement(Caisse $caisse)
    {
        if (!auth()->user()->can('create-caisses')) {
            return redirect()->route('caisses.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer un mouvement.');
        }

        $caisses = Caisse::all();
        return view('caisses.mouvements.create', compact('caisses', 'caisse'));
    }

    public function storeMouvement(Request $request)
    {
        try {
            $caisse = Caisse::findOrFail($request->id_caisse);

            // Vérifier si la caisse est active
            if (!$caisse->canPerformOperations()) {
                throw new \Exception("Cette caisse est inactive. Aucune opération n'est autorisée.");
            }

            Log::info('Début storeMouvement', [
                'request' => $request->all(),
                'user' => auth()->user()->id_util
            ]);

            // Validation de base pour tous les types de mouvements
            $rules = [
                'id_caisse' => 'required|exists:caisses,id_caisse',
                'type_mouvement' => 'required|in:emprunt,remboursement,retrait,pret',
                'montant' => 'required|numeric|min:0',
                'motif' => 'required|string|max:255',
            ];

            // Validation spécifique pour les remboursements
            if ($request->input('type_mouvement') === 'remboursement') {
                $rules['type_operation'] = 'required|in:emprunt,pret';
                $rules['motif_reference'] = [
                    'required',
                    'exists:mouvements_caisse,id_mouvement',
                    function ($attribute, $value, $fail) use ($request) {
                        $mouvement = MouvementCaisse::find($value);
                        if (!$mouvement) {
                            $fail('L\'opération sélectionnée n\'existe pas.');
                            return;
                        }
                        if ($mouvement->type_mouvement !== $request->input('type_operation')) {
                            $fail('Le type d\'opération ne correspond pas à l\'opération sélectionnée.');
                        }
                        if (($mouvement->montant_restant ?? $mouvement->montant) <= 0) {
                            $fail('Cette opération a déjà été entièrement remboursée.');
                        }
                    }
                ];
            }

            $validated = $request->validate($rules, [
                'id_caisse.required' => 'La caisse est requise.',
                'id_caisse.exists' => 'La caisse sélectionnée n\'existe pas.',
                'type_mouvement.required' => 'Le type de mouvement est requis.',
                'type_mouvement.in' => 'Type de mouvement invalide.',
                'montant.required' => 'Le montant est requis.',
                'montant.numeric' => 'Le montant doit être un nombre.',
                'montant.min' => 'Le montant ne peut pas être négatif.',
                'motif.required' => 'Le motif est requis.',
                'motif.max' => 'Le motif ne peut pas dépasser 255 caractères.',
                'type_operation.required' => 'Le type d\'opération est requis pour un remboursement.',
                'type_operation.in' => 'Type d\'opération invalide.',
                'motif_reference.required' => 'Veuillez sélectionner l\'opération à rembourser.',
                'motif_reference.exists' => 'L\'opération sélectionnée n\'existe pas.'
            ]);

            DB::beginTransaction();

            $caisse = Caisse::lockForUpdate()->findOrFail($validated['id_caisse']);
            $montant = (float)$validated['montant'];
            $soldeAvant = (float)$caisse->balance_caisse;
            $userId = auth()->user()->id_util;

            // Gestion spécifique pour les remboursements
            if ($validated['type_mouvement'] === 'remboursement' && isset($validated['motif_reference'])) {
                $empruntOriginal = MouvementCaisse::lockForUpdate()->findOrFail($validated['motif_reference']);
                $montantRestant = $empruntOriginal->montant_restant ?? $empruntOriginal->montant;

                if ($montant > $montantRestant) {
                    throw new \Exception("Le montant du remboursement ({$montant}) ne peut pas dépasser le montant restant à rembourser ({$montantRestant})");
                }

                $empruntOriginal->montant_restant = $montantRestant - $montant;
                $empruntOriginal->save();

                $validated['motif'] = "Remboursement de " . strtolower($empruntOriginal->type_mouvement) . " : " . $empruntOriginal->motif;
            }

            // Traitement selon le type de mouvement avec vérification du solde
            switch ($validated['type_mouvement']) {
                case 'emprunt':
                    // Pour un emprunt, on augmente la balance de la caisse
                    $caisse->balance_caisse = $soldeAvant + $montant;
                    $totalField = 'total_' . Str::plural($validated['type_mouvement']);
                    $caisse->$totalField = (float)($caisse->$totalField ?? 0) + $montant;
                    break;

                case 'retrait':
                case 'pret':
                    // Pour un retrait ou un prêt, on vérifie le solde et on diminue la balance
                    if ($montant > $soldeAvant) {
                        throw new \Exception("Solde insuffisant pour ce " . strtolower($validated['type_mouvement']));
                    }
                    $caisse->balance_caisse = $soldeAvant - $montant;
                    $totalField = 'total_' . Str::plural($validated['type_mouvement']);
                    $caisse->$totalField = (float)($caisse->$totalField ?? 0) + $montant;
                    break;

                case 'remboursement':
                    // Le comportement du remboursement dépend du type d'opération remboursée
                    if ($request->input('type_operation') === 'emprunt') {
                        // Remboursement d'un emprunt : on diminue la balance
                        if ($montant > $soldeAvant) {
                            throw new \Exception("Solde insuffisant pour ce remboursement");
                        }
                        $caisse->balance_caisse = $soldeAvant - $montant;
                    } else {
                        // Remboursement d'un prêt : on augmente la balance
                        $caisse->balance_caisse = $soldeAvant + $montant;
                    }
                    $caisse->total_remboursements = (float)($caisse->total_remboursements ?? 0) + $montant;
                    break;
            }

            $caisse->save();

            // Création du mouvement avec traçabilité
            $mouvement = MouvementCaisse::create([
                'caisse_id' => $caisse->id_caisse,
                'type_mouvement' => $validated['type_mouvement'],
                'montant' => $montant,
                'motif' => $validated['motif'],
                'solde_avant' => $soldeAvant,
                'solde_apres' => $caisse->balance_caisse,
                'user_id' => $userId,
                'motif_reference' => $validated['motif_reference'] ?? null,
                'montant_restant' => in_array($validated['type_mouvement'], ['emprunt', 'pret']) ? $montant : null
            ]);

            DB::commit();

            Log::info('Mouvement enregistré avec succès', [
                'mouvement_id' => $mouvement->id_mouvement,
                'type' => $validated['type_mouvement'],
                'montant' => $montant,
                'caisse_id' => $caisse->id_caisse
            ]);

            return redirect()
                ->route('caisses.index')
                ->with('success', 'Mouvement enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur dans storeMouvement', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

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
        $caisse = Caisse::first();
        if (!$caisse->canPerformOperations()) {
            return back()->with('error', "Cette caisse est inactive. Aucune opération n'est autorisée.");
        }
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
        $caisse = Caisse::first();
        if (!$caisse->canPerformOperations()) {
            return back()->with('error', "Cette caisse est inactive. Aucune opération n'est autorisée.");
        }
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
        try {
            $caisse = Caisse::first();
            if (!$caisse->canPerformOperations()) {
                throw new \Exception("Cette caisse est inactive. Aucune opération n'est autorisée.");
            }
            // Récupérer la caisse
            $caisse = Caisse::first();
            DB::beginTransaction();
            // Vérifier le type de remboursement (emprunt ou prêt)
            if ($request->type === 'pret') {
                // Vérifier que le prêt à rembourser existe
                $pret = MouvementCaisse::where('type_mouvement', 'pret')
                    ->where('id_mouvement', $request->mouvement_id)
                    ->firstOrFail();
                if ($pret->montant_restant < $request->montant) {
                    throw new \Exception('Le montant du remboursement dépasse le montant restant du prêt.');
                }
                // Mettre à jour le montant restant du prêt
                $pret->montant_restant -= $request->montant;
                $pret->save();
                // Mettre à jour la caisse
                $caisse->update([
                    'balance_caisse' => $caisse->balance_caisse + $request->montant,
                    'total_prets' => $caisse->total_prets - $request->montant
                ]);
                // Créer un mouvement de remboursement
                MouvementCaisse::create([
                    'caisse_id' => $caisse->id_caisse,
                    'type_mouvement' => 'remboursement',
                    'montant' => $request->montant,
                    'motif' => "Remboursement du prêt : " . $pret->motif,
                    'solde_avant' => $caisse->balance_caisse - $request->montant,
                    'solde_apres' => $caisse->balance_caisse,
                    'user_id' => auth()->user()->id_util,
                    'motif_reference' => $pret->id_mouvement
                ]);
            } else {
                // Logique existante pour les emprunts Sim Hall
                if ($caisse->emprunt_sim_hall < $request->montant) {
                    throw new \Exception('Le montant du remboursement dépasse le montant de l\'emprunt.');
                }
                $caisse->update([
                    'balance_caisse' => $caisse->balance_caisse - $request->montant,
                    'emprunt_sim_hall' => $caisse->emprunt_sim_hall - $request->montant,
                    'remboursement_sim_hall' => $caisse->remboursement_sim_hall + $request->montant,
                ]);
            }
            DB::commit();
            return back()->with('success', 'Remboursement effectué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du remboursement', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
    public function pretCaisse(Request $request)
    {

        try {
            // Récupérer la caisse
            $caisse = Caisse::first();
            // Vérifier que la caisse a suffisamment de fonds
            if ($caisse->balance_caisse < $request->montant) {
                return back()->with('error', 'Fonds insuffisants dans la caisse pour ce prêt.');
            }
            DB::beginTransaction();
            // Effectuer le prêt et mettre à jour la caisse
            $caisse->update([
                'balance_caisse' => $caisse->balance_caisse - $request->montant,
                'total_prets' => ($caisse->total_prets ?? 0) + $request->montant,
            ]);
            // Créer un mouvement pour le prêt
            MouvementCaisse::create([
                'caisse_id' => $caisse->id_caisse,
                'type_mouvement' => 'pret',
                'montant' => $request->montant,
                'motif' => $request->motif ?? 'Prêt',
                'solde_avant' => $caisse->balance_caisse + $request->montant,
                'solde_apres' => $caisse->balance_caisse,
                'user_id' => auth()->user()->id_util,
                'montant_restant' => $request->montant
            ]);
            DB::commit();
            return back()->with('success', 'Prêt effectué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du prêt', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors du prêt : ' . $e->getMessage());
        }
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

    public function getOperationsNonRemboursees(Request $request, $caisse)
    {
        try {
            $type = $request->query('type');

            $operations = MouvementCaisse::where('caisse_id', $caisse)
                ->where('type_mouvement', $type)
                ->whereNull('deleted_at')
                ->whereRaw('COALESCE(montant_restant, montant) > 0')
                ->select([
                    'id_mouvement',
                    'motif',
                    'montant',
                    'montant_restant',
                    'created_at'
                ])
                ->get()
                ->map(function ($operation) {
                    return [
                        'id_mouvement' => $operation->id_mouvement,
                        'motif' => $operation->motif,
                        'montant_restant' => number_format($operation->montant_restant ?? $operation->montant, 0, ',', ' '),
                        'date' => $operation->created_at->format('d/m/Y'),
                    ];
                });

            return response()->json($operations);
        } catch (\Exception $e) {
            Log::error('Erreur dans getOperationsNonRemboursees: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des opérations'], 500);
        }
    }

    public function getMouvementDetails($id)
    {
        try {
            $mouvement = MouvementCaisse::findOrFail($id);

            return response()->json([
                'solde_avant' => $mouvement->solde_avant,
                'solde_apres' => $mouvement->solde_apres,
                'type_mouvement' => $mouvement->type_mouvement,
                'montant' => $mouvement->montant,
                'motif' => $mouvement->motif,
                'date' => $mouvement->created_at->format('d/m/Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des détails'], 500);
        }
    }

    public function toggleStatus(string $id)
    {
        try {
            $caisse = Caisse::findOrFail($id);

            // Ajoutez un log pour déboguer
            \Log::info('Toggle status pour caisse', [
                'id' => $id,
                'ancien_status' => $caisse->status,
                'nouveau_status' => !$caisse->status
            ]);

            $caisse->status = !$caisse->status;
            $caisse->save();

            return redirect()->back()->with('success', 'Statut mis à jour avec succès');
        } catch (\Exception $e) {
            // Ajoutez un log d'erreur
            \Log::error('Erreur toggle status', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du statut : ' . $e->getMessage());
        }
    }
}
