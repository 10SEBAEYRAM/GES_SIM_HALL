<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <!-- Lien vers Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lien vers Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lien vers FontAwesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Animation pour les cartes */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="max-w-7xl mx-auto py-12 px-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tableau de Bord</h1>

        <!-- Grid pour organiser les cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Section Utilisateurs -->
            <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-users mr-3 text-blue-500"></i> Utilisateurs
                </h2>
                <p class="text-lg mt-2" id="total-users">Total : {{ $dashboardData['totalUsers']['total'] }}</p>
                <p class="text-lg" id="user-evolution">Évolution : {{ $dashboardData['totalUsers']['evolution'] }}%</p>
            </div>

            <!-- Section Produits -->
            <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-box mr-3 text-green-500"></i> Produits
                </h2>
                <p class="text-lg mt-2" id="total-products">Total : {{ $dashboardData['totalProducts']['total'] }}</p>
            </div>

            <!-- Section Transactions -->
            <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-exchange-alt mr-3 text-yellow-500"></i> Transactions
                </h2>
                <p class="text-lg mt-2" id="total-transactions">Montant total : {{ $dashboardData['totalTransactions']['montant'] }}</p>
                <p class="text-lg" id="transaction-evolution">Évolution : {{ $dashboardData['totalTransactions']['evolution'] }}%</p>
            </div>

            <!-- Section Solde Caisse -->
            <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-cash-register mr-3 text-red-500"></i> Solde Caisse
                </h2>
                <p class="text-lg mt-2" id="solde-caisse">Montant : {{ $dashboardData['soldeCaisse']['montant'] }}</p>
                <p class="text-lg" id="caisse-evolution">Évolution : {{ $dashboardData['soldeCaisse']['evolution'] }}%</p>
            </div>

        </div>

        <!-- Graphique des Transactions par Mois -->
        <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-line mr-3 text-blue-500"></i> Transactions par Mois
            </h2>
            <canvas id="transactionsChart" class="mt-4"></canvas>
        </div>

        <!-- Section Soldes des Produits -->
        <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-cogs mr-3 text-purple-500"></i> Soldes des Produits
            </h2>
            <ul class="mt-4 space-y-2" id="produits-balances">
                @foreach ($dashboardData['produitsBalances'] as $produit)
                    <li class="text-lg">{{ $produit['nom_prod'] }} : {{ $produit['balance'] }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Transactions Récentes -->
        <div class="card bg-white shadow-lg rounded-lg p-6 mb-6 hover:shadow-xl">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history mr-3 text-teal-500"></i> Transactions Récentes
            </h2>
            <ul class="mt-4 space-y-2" id="recent-transactions">
                @foreach ($dashboardData['recentTransactions'] as $transaction)
                    <li class="text-lg">{{ $transaction->created_at }} - {{ $transaction->produit->nom_prod }} - {{ $transaction->montant_trans }}</li>
                @endforeach
            </ul>
        </div>
        
    </div>

    <!-- Script pour afficher le graphique des transactions par mois -->
    <script>
        var ctx = document.getElementById('transactionsChart').getContext('2d');
        var transactionsData = @json($dashboardData['transactionsParMois']); 

        var transactionsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: transactionsData.map(transaction => transaction.mois),
                datasets: [{
                    label: 'Montant des Transactions',
                    data: transactionsData.map(transaction => transaction.montant),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Transactions par Mois'
                    }
                }
            }
        });
    </script>
</body>
</html>
