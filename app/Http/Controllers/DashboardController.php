<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produit;
use App\Models\Transaction;
use App\Models\Caisse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $request->input('date_range', 'today');
        
        // Définir la plage de dates en fonction du filtre
        $startDate = match($dateRange) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::today()
        };

        // Calcul des évolutions (comparaison avec la période précédente)
        $previousStartDate = clone $startDate;
        $previousStartDate->sub(match($dateRange) {
            'today' => '1 day',
            'week' => '1 week',
            'month' => '1 month',
            'year' => '1 year'
        });

        // Statistiques des utilisateurs
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $totalUsers = User::count(); // Récupérer le nombre total d'utilisateurs

        // Statistiques des produits
        $activeProducts = Produit::where('actif', true)->count();
        
        // Statistiques des transactions
        $currentTransactions = Transaction::where('created_at', '>=', $startDate)
            ->sum('montant_trans');
        $previousTransactions = Transaction::whereBetween('created_at', [$previousStartDate, $startDate])
            ->sum('montant_trans');
            
        // Solde caisse
        $soldeCaisse = Caisse::sum('balance_caisse');
        $previousSoldeCaisse = Transaction::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $startDate)
            ->sum('solde_caisse_apres');

        // Transactions par mois
        $transactionsParMois = Transaction::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as mois,
            SUM(montant_trans) as montant
        ')
        ->groupBy('mois')
        ->orderBy('mois')
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

        // Passer les données à la vue
        $dashboardData = [
            'totalUsers' => [
                'total' => $totalUsers,
                'evolution' => $previousUsers > 0 
                    ? (($currentUsers - $previousUsers) / $previousUsers) * 100 
                    : 0
            ],
            'totalProducts' => [
                'total' => $activeProducts,
                'evolution' => 0 // À implémenter selon vos besoins
            ],
            'totalTransactions' => [
                'montant' => $currentTransactions,
                'evolution' => $previousTransactions > 0 
                    ? (($currentTransactions - $previousTransactions) / $previousTransactions) * 100 
                    : 0
            ],
            'soldeCaisse' => [
                'montant' => $soldeCaisse,
                'evolution' => $previousSoldeCaisse > 0 
                    ? (($soldeCaisse - $previousSoldeCaisse) / $previousSoldeCaisse) * 100 
                    : 0
            ],
            'transactionsParMois' => $transactionsParMois,
            'produitsBalances' => $produitsBalances,
            'recentTransactions' => $recentTransactions
        ];

        return view('dashboard.index', compact('dashboardData'));
    }
}
