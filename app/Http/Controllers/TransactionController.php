<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Produit;
use App\Models\Caisse;
use App\Services\TransactionService;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->middleware('auth');
        $this->middleware('permission:create-transaction')->only(['create', 'store']);
        $this->middleware('permission:edit-transaction')->only(['edit', 'update']);
    }

    public function index()
    {
        $transactions = Transaction::with(['typeTransaction', 'produit', 'user'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $produits = Produit::where('actif', true)
            ->select('id', 'nom', 'balance') // Optimisation : sélectionner uniquement les champs nécessaires
            ->get();

        return view('transactions.create', compact('produits'));
    }

    public function store(TransactionRequest $request)
    {
        try {
            // Pas besoin de DB::beginTransaction() ici car il est déjà géré dans le TransactionService
            $transaction = $this->transactionService->createTransaction($request->validated());

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction créée avec succès');

        } catch (\App\Exceptions\InsufficientBalanceException $e) {
            return redirect()
                ->back()
                ->with('error', 'Solde insuffisant: ' . $e->getMessage())
                ->withInput();

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la création de la transaction: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['typeTransaction', 'produit', 'user']);
        return view('transactions.show', compact('transaction'));
    }
}