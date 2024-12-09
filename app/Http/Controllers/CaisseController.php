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
                // Colonnes personnalisées
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
        return view('caisses.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'balance_caisse' => 'required|numeric',
        ]);

        Caisse::create($request->all());
        return redirect()->route('caisses.index')->with('success', 'Caisse créée avec succès');
    }


    public function edit($id)
    {
        $caisse = Caisse::findOrFail($id);
        return view('caisses.edit', compact('caisse'));
    }


    public function update(Request $request, $id)
    {
        $caisse = Caisse::findOrFail($id);

        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'balance_caisse' => 'required|numeric',
        ]);

        $caisse->update($validated);

        return redirect()->route('caisses.index')->with('success', 'Caisse mise à jour avec succès.');
    }
    // Supprimer une caisse via AJAX
    public function Destroy($id)
    {
        $caisse = Caisse::findOrFail($id);

        try {
            $caisse->delete();
            return response()->json([
                'success' => true,
                'message' => 'Caisse supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}
