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

        <!-- Cartes des statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Transactions -->
            <div class="card bg-yellow-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-exchange-alt mr-3 text-yellow-500"></i> Transactions
                </h2>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($dashboardData['totalTransactions']['montant'], 2, ',', ' ') }} FCFA
                </p>
                <p class="mt-2 flex items-center {{ $dashboardData['totalTransactions']['evolution'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-{{ $dashboardData['totalTransactions']['evolution'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-2"></i>
                    {{ number_format(abs($dashboardData['totalTransactions']['evolution']), 1) }}%
                </p>
            </div>

            <!-- Utilisateurs -->
            <div class="card bg-blue-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-users mr-3 text-blue-500"></i> Utilisateurs
                </h2>
                <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['totalUsers']['total'] }}</p>
            </div>

            <!-- Produits -->
            <div class="card bg-purple-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-box mr-3 text-purple-500"></i> Produits Actifs
                </h2>
                <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['totalProducts']['total'] }}</p>
            </div>

            <!-- Solde Caisse -->
            <div class="card bg-green-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-cash-register mr-3 text-green-500"></i> Solde Caisse
                </h2>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($dashboardData['soldeCaisse']['montant'], 2, ',', ' ') }} FCFA
                </p>
            </div>
        </div>

        <!-- Solde des Produits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($dashboardData['produitsBalances'] as $produit)
                <div class="card bg-teal-100 shadow-lg rounded-lg p-6 hover:shadow-xl">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $produit->nom_prod }}</h2>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($produit->balance, 2, ',', ' ') }} FCFA
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Transactions par Période -->
            <div class="card bg-white shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-chart-line mr-3 text-blue-500"></i> Transactions par Période
                </h2>
                <div class="mb-4">
                    <select id="periodSelect" class="w-full p-2 border rounded" onchange="updateChart()">
                        <option value="day">Par Jour</option>
                        <option value="week">Par Semaine</option>
                        <option value="month" selected>Par Mois</option>
                        <option value="year">Par Année</option>
                    </select>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="transactionsChart"></canvas>
                </div>
            </div>

            <!-- Répartition des Transactions -->
            <div class="card bg-white shadow-lg rounded-lg p-6 hover:shadow-xl">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center mb-4">
                    <i class="fas fa-chart-pie mr-3 text-green-500"></i> Répartition des Transactions
                </h2>
                <div class="relative" style="height: 300px;">
                    <canvas id="transactionsPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

   <script>
   // Fonction de mise à jour du graphique selon la période
    function updateChart() {
        const selectedPeriod = document.getElementById('periodSelect').value;
        
        let newLabels, newData;

        switch (selectedPeriod) {
            case 'day':
                newLabels = @json($dashboardData['transactionsParJour']->pluck('jour'));
                newData = @json($dashboardData['transactionsParJour']->pluck('montant'));
                break;
            case 'week':
                newLabels = @json($dashboardData['transactionsParSemaine']->pluck('semaine'));
                newData = @json($dashboardData['transactionsParSemaine']->pluck('montant'));
                break;
            case 'month':
                newLabels = @json($dashboardData['transactionsParMois']->pluck('mois'));
                newData = @json($dashboardData['transactionsParMois']->pluck('montant'));
                break;
            case 'year':
                newLabels = @json($dashboardData['transactionsParAnnee']->pluck('annee'));
                newData = @json($dashboardData['transactionsParAnnee']->pluck('montant'));
                break;
        }

        // Mise à jour des graphiques
        transactionsChart.data.labels = newLabels;
        transactionsChart.data.datasets[0].data = newData;
        transactionsChart.update();

        transactionsPieChart.data.labels = newLabels;
        transactionsPieChart.data.datasets[0].data = newData;
        transactionsPieChart.update();
    }

    // Initialiser les graphiques
    const labels = @json($dashboardData['transactionsParMois']->pluck('mois'));
    const data = @json($dashboardData['transactionsParMois']->pluck('montant'));

    const ctxBar = document.getElementById('transactionsChart').getContext('2d');
    const transactionsChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Transactions par Mois',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
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

    const ctxPie = document.getElementById('transactionsPieChart').getContext('2d');
    const transactionsPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

</body>
</html>
