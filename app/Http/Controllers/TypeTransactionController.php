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
        return view('type_transactions.create');
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
        $typeTransaction = TypeTransaction::findOrFail($id);
        return view('type_transactions.edit', compact('typeTransaction'));
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
