<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produit;
use App\Models\Transaction;
use App\Models\Caisse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $request->input('date_range', 'today');

        // Définir la plage de dates en fonction du filtre
        $startDate = match ($dateRange) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::today()
        };

        // Calcul de la période précédente
        $previousStartDate = (clone $startDate)->modify(match ($dateRange) {
            'today' => '-1 day',
            'week' => '-1 week',
            'month' => '-1 month',
            'year' => '-1 year',
        });

        // Calcul des évolutions
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $totalUsers = User::count();

        $activeProducts = Produit::where('actif', true)->count();

        // Transactions
        $currentTransactions = Transaction::where('created_at', '>=', $startDate)->sum('montant_trans');
        $previousTransactions = Transaction::whereBetween('created_at', [$previousStartDate, $startDate])->sum('montant_trans');

        // Solde caisse
        $soldeCaisse = Caisse::sum('balance_caisse');
        $previousSoldeCaisse = Caisse::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $startDate)
            ->sum('balance_caisse');

        // Transactions par mois
        // Transactions par mois
$transactionsParMois = Transaction::selectRaw('
DATE_FORMAT(created_at, "%Y-%m") as mois,
SUM(montant_trans) as montant
')
->groupBy('mois')
->orderBy('mois', 'asc') // Ajout de la direction "asc" ou "desc"
->get();

// Transactions par jour
$transactionsParJour = Transaction::selectRaw('
DATE(created_at) as jour,
SUM(montant_trans) as montant
')
->groupBy('jour')
->orderBy('jour', 'asc') // Direction spécifiée
->get();

// Transactions par semaine
$transactionsParSemaine = Transaction::selectRaw('
YEAR(created_at) as annee,
WEEK(created_at) as semaine,
SUM(montant_trans) as montant
')
->groupBy('annee', 'semaine')
->orderBy('annee', 'asc') // Ajout de la direction
->orderBy('semaine', 'asc') // Ajout de la direction
->get();

// Transactions par année
$transactionsParAnnee = Transaction::selectRaw('
YEAR(created_at) as annee,
SUM(montant_trans) as montant
')
->groupBy('annee')
->orderBy('annee', 'asc') // Direction spécifiée
->get();


        // Soldes des produits
        $produitsBalances = Produit::where('actif', true)
            ->select('nom_prod', 'balance')
            ->get();

        // Transactions récentes
        $recentTransactions = Transaction::with(['produit', 'typeTransaction'])
            ->latest()
            ->take(10)
            ->get();

        // Calcul de l'évolution
        $dashboardData = [
            'totalUsers' => [
                'total' => $totalUsers,
                'evolution' => $this->calculateEvolution($currentUsers, $previousUsers)
            ],
            'totalProducts' => [
                'total' => $activeProducts,
                'evolution' => 0 // Si nécessaire, ajouter un calcul pour ce champ
            ],
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

        return view('dashboard.index', compact('dashboardData'));
    }

    private function calculateEvolution($current, $previous)
    {
        return $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
    }

    public function getTransactionsByPeriod($period)
    {
        $data = [];
        
        switch ($period) {
            case 'day':
                $data['labels'] = $this->getTransactionsByDayLabels();
                $data['data'] = $this->getTransactionsByDayData();
                break;
            case 'week':
                $data['labels'] = $this->getTransactionsByWeekLabels();
                $data['data'] = $this->getTransactionsByWeekData();
                break;
            case 'month':
                $data['labels'] = $this->getTransactionsByMonthLabels();
                $data['data'] = $this->getTransactionsByMonthData();
                break;
            case 'year':
                $data['labels'] = $this->getTransactionsByYearLabels();
                $data['data'] = $this->getTransactionsByYearData();
                break;
        }

        return response()->json($data);
    }

    public function getTransactionsByMonthLabels()
    {
        return DB::table('transactions')
            ->select(DB::raw('MONTH(created_at) as month'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
    }

    public function getTransactionsByMonthData()
    {
        return DB::table('transactions')
            ->select(DB::raw('SUM(montant_trans) as montant'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
    }

    // Ajoutez des méthodes similaires pour semaine, jour et année si nécessaire
}
