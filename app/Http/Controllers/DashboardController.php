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
        $type_transactions = DB::table('transactions')
            ->join('type_transactions', 'type_transactions.id_type_transa', '=', 'type_transaction_id')
            ->select('type_transactions.nom_type_transa', DB::raw('SUM(transactions.montant_trans) as total'))
            ->groupBy('type_transactions.nom_type_transa')
            ->get();

        // Gérer les données vides
        if ($type_transactions->isEmpty()) {
            $type_transactions = collect(); // Crée une collection vide
        }

        // Conversion en tableau et récupération des clés
        $transactionsArray = $type_transactions->toArray();
        $keys = array_keys($transactionsArray);

        // Récupérer les utilisateurs créés dans les 7 derniers jours
        $nouveauxUtilisateurs = User::where('created_at', '>=', now()->subDays(7))->get();

        // Récupérer le nombre total d'utilisateurs
        $totalUtilisateurs = User::count();

        // Définir les variables manquantes
        $totalProduits = Produit::sum('balance');  // Somme des balances des produits
        $totalTransactions = Transaction::sum('montant_trans');  // Somme des montants des transactions
        $balanceProduits = Produit::sum('balance');  // Somme des soldes des produits (ou selon votre logique)

        // Définir le montant total de la caisse
        $montantCaisse = Caisse::sum('balance_caisse');  // Total des soldes des caisses (ou ajustez si nécessaire)

        // Récupérer toutes les transactions ou celles dans la période spécifiée
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();

        // Définir les dates des transactions
        $transactionDates = $transactions->pluck('created_at')->unique()->sort();  // Récupère les dates uniques triées

        // Calculer les montants des transactions
        $transactionAmounts = $transactions->sum('montant_trans');  // Somme des montants des transactions

        $users = User::all(); // Exemple pour récupérer tous les utilisateurs

        // Passer les données à la vue
        return view('dashboard.index', compact(
            'dashboardData',
            'type_transactions',
            'keys',
            'nouveauxUtilisateurs',
            'totalUtilisateurs',
            'totalProduits',
            'totalTransactions',
            'balanceProduits',
            'montantCaisse',
            'transactions',
            'users',
            'transactionAmounts',
            'transactionDates',  // Passer la variable transactionDates
            'startDate',
            'endDate',
            'previousStartDate',
            'previousEndDate'
        ));
    }




    public function filter(Request $request)
    {
        $period = $request->query('period'); // Ex : 'jour'
        $product = $request->query('product'); // Ex : 1

        // Logique pour filtrer les données selon la période et le produit
        return response()->json([
            'success' => true,
            'period' => $period,
            'product' => $product,
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
