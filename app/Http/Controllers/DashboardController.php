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

        // Statistiques des utilisateurs
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $totalUsers = User::count();

        // Produits actifs
        $activeProducts = Produit::where('actif', true)->count();

        // Transactions
        $currentTransactions = Transaction::where('created_at', '>=', $startDate)->sum('montant_trans');
        $previousTransactions = Transaction::whereBetween('created_at', [$previousStartDate, $startDate])->sum('montant_trans');

        // Solde caisse
        $soldeCaisse = Caisse::sum('balance_caisse');
        $previousSoldeCaisse = Caisse::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $startDate)
            ->sum('balance_caisse');

        // Transactions par mois pour le graphique de ligne
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

        // Répartition des types de transactions pour le graphique circulaire
        $transactionTypesDistribution = $this->getTransactionTypesDistribution($startDate);

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
            'produitsBalances' => $produitsBalances,
            'recentTransactions' => $recentTransactions,
            'transactionTypesDistribution' => $transactionTypesDistribution // Nouvelle clé pour la répartition des transactions
        ];

        return view('dashboard.index', compact('dashboardData'));
    }

    /**
     * Calcule l'évolution en pourcentage entre une valeur actuelle et une valeur précédente.
     */
    private function calculateEvolution($current, $previous)
    {
        return $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
    }

    /**
     * Récupère la répartition des montants des transactions par type pour une période donnée.
     */
    private function getTransactionTypesDistribution($startDate)
    {
        return Transaction::selectRaw('type_transaction_id, SUM(montant_trans) as montant')
            ->where('created_at', '>=', $startDate)
            ->groupBy('type_transaction_id')
            ->with('typeTransaction') // Assurez-vous que la relation `typeTransaction` est bien définie dans le modèle `Transaction`
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->typeTransaction->nom_type_transa,
                    'amount' => $item->montant
                ];
            });
    }
}
