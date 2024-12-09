<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produit;
use App\Models\Transaction;
use App\Models\Caisse;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer la plage de dates sélectionnée ou par défaut (aujourd'hui)
        $dateRange = $request->input('date_range', 'today');

        // Définir les plages de dates actuelles et précédentes
        [$startDate, $endDate] = $this->getDateRange($dateRange);
        [$previousStartDate, $previousEndDate] = $this->getPreviousDateRange($dateRange);

        // Récupérer les données du tableau de bord
        $dashboardData = $this->getDashboardData($startDate, $endDate, $previousStartDate, $previousEndDate);

        // Ajouter les caisses au tableau des données
        $dashboardData['caisses'] = Caisse::all();

        // Transactions groupées par type
        $transactionsByType = Transaction::select('type_transaction_id', DB::raw('SUM(montant_trans) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type_transaction_id')
            ->get();


        // Gérer les données vides
        if ($transactionsByType->isEmpty()) {
            $transactionsByType = collect(); // Crée une collection vide
        }

        // Conversion en tableau et récupération des clés
        $transactionsArray = $transactionsByType->toArray();
        $keys = array_keys($transactionsArray);

        // Passer les données à la vue
        return view('dashboard.index', compact('dashboardData', 'transactionsByType', 'keys'));
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

    private function getFilteredDates($dateFilter)
    {
        $today = Carbon::today();

        switch ($dateFilter) {
            case 'month':
                return [
                    'startDate' => $today->startOfMonth(),
                    'endDate' => $today->endOfMonth(),
                ];
            case 'week':
                return [
                    'startDate' => $today->startOfWeek(),
                    'endDate' => $today->endOfWeek(),
                ];
            case 'day':
                return [
                    'startDate' => $today->startOfDay(),
                    'endDate' => $today->endOfDay(),
                ];
            case 'year':
                return [
                    'startDate' => $today->startOfYear(),
                    'endDate' => $today->endOfYear(),
                ];
            default:
                return [
                    'startDate' => $today->startOfMonth(),
                    'endDate' => $today->endOfMonth(),
                ];
        }
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
        $activeProducts = Produit::where('actif', true)->count();

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
        $produitsBalances = Produit::where('actif', true)
            ->select('nom_prod', 'balance')
            ->get();

        // Transactions récentes
        $recentTransactions = Transaction::with(['produit', 'typeTransaction'])
            ->latest()
            ->take(10)
            ->get();

        // Ajouter les produits aux données
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
