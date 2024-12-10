<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        canvas {
            display: block;
            width: 100% !important;
            height: 300px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="max-w-7xl mx-auto py-12 px-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tableau de Bord</h1>

        <!-- Section des statistiques principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Transactions par type -->
            @forelse ($transactionsByType as $transaction)
            <div class="card bg-yellow-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-exchange-alt mr-3 text-yellow-500"></i> Transaction Type {{ $transaction->type_transaction_id }}
                </h2>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($transaction->total, 2, ',', ' ') }} FCFA
                </p>
            </div>
            @empty
            <div class="col-span-full text-center">
                <p class="text-gray-600 text-lg">Aucune donnée de transaction disponible pour cette période.</p>
            </div>
            @endforelse
        </div>


        <!-- Section des caisses -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Détail des Caisses et Soldes</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse ($dashboardData['caisses'] as $caisse)
            <div class="card bg-green-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $caisse->nom_caisse }}
                </h2>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($caisse->balance_caisse, 2, ',', ' ') }} FCFA
                </p>
            </div>
            @empty
            <div class="col-span-full text-center">
                <p class="text-gray-600 text-lg">Aucune caisse disponible.</p>
            </div>
            @endforelse
        </div>

        <!-- Section des produits -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Produits et Soldes</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse ($dashboardData['produits'] as $produit)
            <div class="card bg-blue-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $produit->nom_prod }}
                </h2>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($produit->balance, 2, ',', ' ') }} FCFA
                </p>
            </div>
            @empty
            <div class="col-span-full text-center">
                <p class="text-gray-600 text-lg">Aucun produit disponible.</p>
            </div>
            @endforelse
        </div>

        <!-- Graphiques -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Graphiques</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Transactions par Type -->
            <div class="card bg-white shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-chart-bar mr-3 text-blue-500"></i> Transactions par Type
                </h2>
                <div class="relative">
                    <canvas id="typeTransactionsChart"></canvas>
                </div>
            </div>
            <!-- Répartition des Transactions -->
            <div class="card bg-white shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-chart-pie mr-3 text-green-500"></i> Répartition des Transactions
                </h2>
                <div class="relative">
                    <canvas id="transactionsPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const types = @json(isset($dashboardData['transactionsByType']) ? array_keys($dashboardData['transactionsByType']) : []);
        const amounts = @json(isset($dashboardData['transactionsByType']) ? array_map(fn($t) => $t['montant'], $dashboardData['transactionsByType']) : []);

        // Graphique des transactions par type
        const ctxType = document.getElementById('typeTransactionsChart').getContext('2d');
        new Chart(ctxType, {
            type: 'bar',
            data: {
                labels: types,
                datasets: [{
                    label: 'Montant par Type',
                    data: amounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique circulaire
        const ctxPie = document.getElementById('transactionsPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: types,
                datasets: [{
                    data: amounts,
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FFC300', '#DAF7A6'],
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>