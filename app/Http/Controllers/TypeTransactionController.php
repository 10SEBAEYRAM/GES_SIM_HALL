<?php

namespace App\Http\Controllers;

use App\Models\TypeTransaction;
use Illuminate\Http\Request;

class TypeTransactionController extends Controller
{
    public function index()
    {
        $typeTransactions = TypeTransaction::paginate(10);
        return view('type_transactions.index', compact('typeTransactions'));
    }

    public function create()
    {
        if (!auth()->user()->can('create-type_transactions')) {
            return redirect()->route('type-transaction.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer un type de transaction.');
        }

        $typeTransactions = TypeTransaction::all(); // Récupérer tous les types de transactions
        return view('type_transactions.create', compact('typeTransactions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom_type_transa' => 'required|string|max:50|unique:type_transactions',
            ]);

            TypeTransaction::create($validated);

            return redirect()
                ->route('type-transactions.index')
                ->with('success', 'Type de transaction créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du type de transaction.');
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('edit-type_transactions')) {
            return redirect()->route('type-transaction.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier un type transaction.');
        }

        $typeTransaction = TypeTransaction::findOrFail($id);
        return view('type-transactions.edit', compact('typeTransaction'));
    }

    public function update(Request $request, $id)
    {
        try {
            $typeTransaction = TypeTransaction::findOrFail($id);

            $validated = $request->validate([
                'nom_type_transa' => 'required|string|max:50|unique:type_transactions,nom_type_transa,' . $id . ',id_type_transa',
            ]);

            $typeTransaction->update($validated);

            return redirect()
                ->route('type-transactions.index')
                ->with('success', 'Type de transaction mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du type de transaction.');
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('delete-types_transactions')) {
            return redirect()->route('types-transaction.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer un type de transaction.');
        }
        try {
            $typeTransaction = TypeTransaction::findOrFail($id);
            $typeTransaction->delete();

            return redirect()
                ->route('type-transactions.index')
                ->with('success', 'Type de transaction supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('type-transactions.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du type de transaction.');
        }
    }
}
