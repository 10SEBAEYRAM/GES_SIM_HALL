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

        .filter-section {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            justify-content: center;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
<aside class="w-56 bg-white shadow-lg p-6 fixed inset-y-0">
            @include('layouts.navigation') <!-- Sidebar Navigation -->
        </aside>

        <div class="flex-1 ml-56 p-8 bg-gray-50 overflow-auto">

    <div class="max-w-7xl mx-auto py-12 px-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Tableau de Bord</h1>

        <!-- Filtrage dynamique -->
        <div class="filter-section">
            <select id="filter-period" class="px-4 py-2 border rounded-md">
                <option value="jour">Jour</option>
                <option value="semaine">Semaine</option>
                <option value="mois">Mois</option>
                <option value="annee">Année</option>
            </select>
            <select id="filter-produit" class="px-4 py-2 border rounded-md">
                <option value="tous">Tous les produits</option>
                @foreach ($dashboardData['produits'] as $produit)
                <option value="{{ $produit->id_prod }}">{{ $produit->nom_prod }}</option>
                @endforeach
            </select>
            <button id="apply-filters" class="bg-blue-500 text-white px-6 py-2 rounded-md">Appliquer les filtres</button>
        </div>

        <!-- Sections mises à jour dynamiquement -->
        <div id="dashboard-content">
            @include('partials.dashboard-content', ['dashboardData' => $dashboardData])
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
        </div>

    <script>
        // Initialisation des graphiques avec Chart.js
        const ctxType = document.getElementById('typeTransactionsChart').getContext('2d');
        const ctxPie = document.getElementById('transactionsPieChart').getContext('2d');

        // Graphique en barres pour les types de transactions
        let typeTransactionsChart = new Chart(ctxType, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
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

        // Graphique en camembert pour les types de transactions
        let transactionsPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true
            }
        });

        // Gestionnaire d'événement pour le bouton "Appliquer les filtres"
        document.getElementById('apply-filters').addEventListener('click', function() {
            // Récupération des valeurs des filtres
            const period = document.getElementById('filter-period').value;
            const produit = document.getElementById('filter-produit').value;

            // Requête AJAX pour récupérer les données filtrées
            fetch(`/dashboard/filter?period=${encodeURIComponent(period)}&produit=${encodeURIComponent(produit)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP : ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues :', data); // Affiche les données dans la console pour debug

                    // Vérification des données retournées
                    if (!data || !data.typeTransactions || !data.typeTransactions.labels || !data.typeTransactions.data) {
                        console.error('Données manquantes ou invalides:', data);
                        alert('Les données retournées par le serveur sont incomplètes.');
                        return;
                    }

                    // Mise à jour de la section HTML avec le contenu filtré
                    document.getElementById('dashboard-content').innerHTML = data.htmlContent;

                    // Mise à jour du graphique en barres
                    typeTransactionsChart.data.labels = data.typeTransactions.labels;
                    typeTransactionsChart.data.datasets = [{
                        label: 'Montant par Type',
                        data: data.typeTransactions.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }];
                    typeTransactionsChart.update();

                    // Mise à jour du graphique en camembert
                    transactionsPieChart.data.labels = data.typeTransactions.labels;
                    transactionsPieChart.data.datasets = [{
                        data: data.typeTransactions.data,
                        backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FFC300', '#DAF7A6']
                    }];
                    transactionsPieChart.update();
                })
                .catch(error => {
                    console.error('Erreur lors de la requête ou du traitement des données :', error);
                    alert('Une erreur s’est produite lors de l’application des filtres.');
                });
        });
    </script>

</body>

</html>