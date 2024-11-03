<?php

namespace App\Http\Controllers;

use App\Models\TypeTransaction;
use App\Http\Requests\TypeTransactionRequest;
use Illuminate\Http\Request;

class TypeTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-type-transactions');
    }

    public function index()
    {
        $typeTransactions = TypeTransaction::latest()->paginate(10);
        return view('type-transactions.index', compact('typeTransactions'));
    }

    public function create()
    {
        return view('type-transactions.create');
    }

    public function store(TypeTransactionRequest $request)
    {
        TypeTransaction::create($request->validated());
        return redirect()
            ->route('type-transactions.index')
            ->with('success', 'Type de transaction créé avec succès');
    }

    public function edit(TypeTransaction $typeTransaction)
    {
        return view('type-transactions.edit', compact('typeTransaction'));
    }

    public function update(TypeTransactionRequest $request, TypeTransaction $typeTransaction)
    {
        $typeTransaction->update($request->validated());
        return redirect()
            ->route('type-transactions.index')
            ->with('success', 'Type de transaction modifié avec succès');
    }

    public function destroy(TypeTransaction $typeTransaction)
    {
        $typeTransaction->delete();
        return redirect()
            ->route('type-transactions.index')
            ->with('success', 'Type de transaction supprimé avec succès');
    }
}