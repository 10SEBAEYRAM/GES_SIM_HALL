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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    // Passer les données du contrôleur à JavaScript via la vue Blade
    const transactionsData = @json($dashboardData);

    let period = 'month'; // Période par défaut
    let transactionsChart = null;
    let pieChart = null;

    const chartColors = {
        primary: 'rgba(59, 130, 246, 1)',
        primaryLight: 'rgba(59, 130, 246, 0.2)',
        pieColors: ['#4299E1', '#48BB78', '#ED8936', '#ED64A6', '#9F7AEA', '#667EEA']
    };

    function formatMoney(value) {
        return value.toFixed(2).replace('.', ',');
    }

    function getPeriodLabel(period) {
        const labels = {
            day: 'Jour',
            week: 'Semaine',
            month: 'Mois',
            year: 'Année'
        };
        return labels[period] || 'Mois';
    }

    function createChart(periodData) {
        const ctx = document.getElementById('transactionsChart').getContext('2d');
        
        if (transactionsChart) {
            transactionsChart.destroy();
        }

        transactionsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: periodData.map(item => item.periode),
                datasets: [{
                    label: 'Montant des Transactions (FCFA)',
                    data: periodData.map(item => item.montant),
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primaryLight,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Évolution des Transactions ${getPeriodLabel(period)}`,
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatMoney(context.raw)} FCFA`;
                            }
                        }
                    }
                }
            }
        });
    }

    function createPieChart(transactionTypes) {
        const ctx = document.getElementById('transactionsPieChart').getContext('2d');
        
        if (pieChart) {
            pieChart.destroy();
        }

        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: transactionTypes.map(type => type.name),
                datasets: [{
                    data: transactionTypes.map(type => type.amount),
                    backgroundColor: chartColors.pieColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Répartition des Types de Transactions',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    }

    function updateChart() {
        period = document.getElementById('periodSelect').value;

        if (!transactionsData[period]) {
            console.error(`Aucune donnée trouvée pour la période ${period}`);
            return;
        }

        createChart(transactionsData[period].data);
        createPieChart(transactionsData[period].types);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateChart();
    });
</script>


</body>
</html>