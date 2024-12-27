<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produit;
use App\Models\Transaction;
use App\Models\TypeTransaction;

use App\Models\Caisse;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    protected $dashboardService;
    public function index(Request $request)
{
    try {
        // Récupération des données existantes
        $caisses = Caisse::select('id_caisse', 'nom_caisse', 'balance_caisse')->get();
        $produits = Produit::select('id_prod', 'nom_prod', 'balance')->get();
        
        // Correction de la requête pour les types de transactions
        $typeTransactions = TypeTransaction::select(
                'type_transactions.id_type_transa',
                'type_transactions.nom_type_transa'
            )
            ->leftJoin('transactions', 'type_transactions.id_type_transa', '=', 'transactions.type_transaction_id')
            ->selectRaw('COALESCE(SUM(transactions.montant_trans), 0) as montant_total')
            ->groupBy('type_transactions.id_type_transa', 'type_transactions.nom_type_transa')
            ->get();

        $totalBalance = $caisses->sum('balance_caisse');
        $totalProduits = $produits->sum('balance');
        $totalTransactions = Transaction::sum('montant_trans');

        // Préparation des données pour les graphiques
        $chartData = [
            'labels' => $typeTransactions->pluck('nom_type_transa')->toArray(),
            'values' => $typeTransactions->pluck('montant_total')->toArray()
        ];

        return view('dashboard.index', compact(
            'caisses',
            'produits',
            'typeTransactions',
            'totalBalance',
            'totalProduits',
            'totalTransactions',
            'chartData',
            // 'pieChartData',
            // 'statistics'
            
        ));
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
}


    public function filter(Request $request)
{
    $period = $request->input('period', 'day');
    
    // Définir la plage de dates en fonction de la période
    $startDate = match($period) {
        'day' => now()->startOfDay(),
        'week' => now()->startOfWeek(),
        'month' => now()->startOfMonth(),
        'year' => now()->startOfYear(),
        default => now()->startOfDay(),
    };

    // Récupérer les transactions pour la période
    $transactions = Transaction::with('typeTransaction')
        ->whereBetween('created_at', [$startDate, now()])
        ->get();

    // Préparer les données pour les graphiques
    $chartData = [
        'labels' => [],
        'values' => []
    ];

    // Grouper les transactions par type
    $groupedTransactions = $transactions->groupBy('typeTransaction.nom_type_transa');
    
    foreach($groupedTransactions as $type => $typeTransactions) {
        $chartData['labels'][] = $type;
        $chartData['values'][] = $typeTransactions->sum('montant_trans');
    }

    // Calculer les statistiques
    $statistics = [
        'totalTransactions' => $transactions->sum('montant_trans'),
        'totalCount' => $transactions->count(),
        'averageAmount' => $transactions->avg('montant_trans'),
    ];

    return response()->json([
        'chartData' => $chartData,
        'statistics' => $statistics
    ]);
}


    public function getChartData(Request $request)
    {
        $dateFilter = $request->input('dateFilter', 'month');
        $transactionType = $request->input('transactionType', '');
        $productFilter = $request->input('productFilter', '');

        // Calcul des dates selon le filtre
        $dates = $this->getFilteredDates($dateFilter);

        // Requête pour récupérer les données de transactions filtrées
        $query = Transaction::selectRaw('DATE(created_at) as date, SUM(montant_trans) as total')
            ->groupBy('date')
            ->whereBetween('created_at', [$dates['startDate'], $dates['endDate']]);

        // Ajouter les filtres supplémentaires si sélectionnés
        if ($transactionType) {
            $query->where('type_transaction_id', $transactionType);
        }

        if ($productFilter) {
            $query->where('produit_id', $productFilter);
        }

        $transactions = $query->get();

        // Préparer les données pour le graphique
        $labels = $transactions->pluck('date');
        $totals = $transactions->pluck('total');

        return response()->json([
            'labels' => $labels,
            'totals' => $totals
        ]);
    }

    public function getFilteredData(Request $request)
    {
        $period = $request->input('period', 'jour'); // Valeur par défaut : jour
        $productId = $request->input('product', 'tous'); // Valeur par défaut : tous

        // Définir la période de filtrage en fonction de la période sélectionnée
        $query = Transaction::query();
        switch ($period) {
            case 'jour':
                $query->whereDate('created_at', now()->format('Y-m-d'));
                break;
            case 'semaine':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mois':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'annee':
                $query->whereYear('created_at', now()->year);
                break;
        }

        // Si un produit spécifique est sélectionné
        if ($productId !== 'tous') {
            $query->where('product_id', $productId);
        }

        // Récupérer les transactions et leurs montants
        $typeTransactions = $query->selectRaw('type_transaction, SUM(montant) as total')
            ->groupBy('type_transaction')
            ->get();

        // Répartition des montants par produit
        $products = Produit::all();

        // Récupérer les caisses et leurs soldes
        $caisses = Caisse::all();

        // Préparer les données pour le graphique
        $typeTransactionData = $typeTransactions->mapWithKeys(function ($transaction) {
            return [$transaction->type_transaction => $transaction->total];
        });

        return response()->json([
            'typeTransactions' => [
                'labels' => $typeTransactionData->keys(),
                'data' => $typeTransactionData->values(),
            ],
            'pieChart' => [
                'labels' => $products->pluck('nom_prod'),
                'data' => $products->pluck('balance'),
            ],
            'caisses' => $caisses,
            'produits' => $products,
        ]);
    }
    private function getDateRange($dateRange)
    {
        $startDate = match ($dateRange) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::today(),
        };

        return [$startDate, Carbon::now()];
    }

    private function getPreviousDateRange($dateRange)
    {
        $previousStartDate = match ($dateRange) {
            'today' => Carbon::yesterday(),
            'week' => Carbon::now()->subWeek()->startOfWeek(),
            'month' => Carbon::now()->subMonth()->startOfMonth(),
            'year' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::yesterday(),
        };

        $previousEndDate = match ($dateRange) {
            'today' => $previousStartDate,
            'week' => $previousStartDate->copy()->endOfWeek(),
            'month' => $previousStartDate->copy()->endOfMonth(),
            'year' => $previousStartDate->copy()->endOfYear(),
            default => $previousStartDate,
        };

        return [$previousStartDate, $previousEndDate];
    }

    private function getDashboardData($startDate, $endDate, $previousStartDate, $previousEndDate)
    {
        // Compte total des utilisateurs
        $totalUsers = User::count();
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();

        // Compte des produits actifs
        $activeProducts = Produit::where('status', true)->count();

        // Transactions
        $currentTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->sum('montant_trans');
        $previousTransactions = Transaction::whereBetween('created_at', [$previousStartDate, $startDate])->sum('montant_trans');

        // Solde des caisses
        $soldeCaisse = Caisse::sum('balance_caisse');
        $previousSoldeCaisse = Caisse::whereBetween('created_at', [$previousStartDate, $startDate])->sum('balance_caisse');

        // Transactions par période (mois, jour, semaine, année)
        $transactionsParMois = $this->getTransactionsByPeriod('month');
        $transactionsParJour = $this->getTransactionsByPeriod('day');
        $transactionsParSemaine = $this->getTransactionsByPeriod('week');
        $transactionsParAnnee = $this->getTransactionsByPeriod('year');

        // Solde des produits actifs
        $produitsBalances = Produit::where('status', true)
            ->select('nom_prod', 'balance')
            ->get();

        // Transactions récentes
        $recentTransactions = Transaction::with(['produit', 'typeTransaction'])
            ->latest()
            ->take(10)
            ->get();

        // Ajouter les produits aux donn��es
        $produits = Produit::all(); // Récupérer tous les produits pour l'affichage

        return [
            'totalUsers' => [
                'total' => $totalUsers,
                'evolution' => $this->calculateEvolution($currentUsers, $previousUsers)
            ],
            'totalProducts' => [
                'total' => $activeProducts,
                'evolution' => 0
            ],
            'produits' => $produits, // Ajouter ici la clé 'produits'
            'totalTransactions' => [
                'montant' => $currentTransactions,
                'evolution' => $this->calculateEvolution($currentTransactions, $previousTransactions)
            ],
            'soldeCaisse' => [
                'montant' => $soldeCaisse,
                'evolution' => $this->calculateEvolution($soldeCaisse, $previousSoldeCaisse)
            ],
            'transactionsParMois' => $transactionsParMois,
            'transactionsParJour' => $transactionsParJour,
            'transactionsParSemaine' => $transactionsParSemaine,
            'transactionsParAnnee' => $transactionsParAnnee,
            'produitsBalances' => $produitsBalances,
            'recentTransactions' => $recentTransactions
        ];
    }


    private function calculateEvolution($current, $previous)
    {
        return $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
    }

    private function getTransactionsByPeriod($period)
    {
        $grouping = match ($period) {
            'day' => 'DATE(created_at)',
            'week' => 'YEAR(created_at), WEEK(created_at)',
            'month' => 'YEAR(created_at), MONTH(created_at)',
            'year' => 'YEAR(created_at)',
        };

        return Transaction::selectRaw("$grouping as period, SUM(montant_trans) as montant")
            ->groupByRaw($grouping)
            ->orderByRaw($grouping)
            ->get();
    }
}
