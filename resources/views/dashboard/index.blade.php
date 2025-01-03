@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête animé -->
        <div class="text-center mb-10 animate__animated animate__fadeInDown">
            <h1 class="text-4xl font-bold text-white mb-2">Tableau de Bord</h1>
            <p class="text-gray-300">Supervision en temps réel</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4 mb-8 animate__animated animate__fadeIn">
            <div class="flex flex-wrap gap-4 justify-center">
                <select id="period-filter" class="bg-white/20 text-white border border-white/30 rounded-lg px-4 py-2">
                    <option value="day">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                </select>
            </div>
        </div>

        <!-- Cartes des statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Balance Totale -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 transform hover:scale-105 transition-all duration-300 shadow-lg animate__animated animate__fadeInUp">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100">Balance Totale</p>
                        <h3 class="text-2xl font-bold text-white mt-2">{{ number_format($totalBalance, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="bg-blue-400/30 rounded-full p-3">
                        <i class="fas fa-wallet text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Total Produits -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 transform hover:scale-105 transition-all duration-300 shadow-lg animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100">Balance Produits</p>
                        <h3 class="text-2xl font-bold text-white mt-2">{{ number_format($totalProduits, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="bg-emerald-400/30 rounded-full p-3">
                        <i class="fas fa-box text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl p-6 transform hover:scale-105 transition-all duration-300 shadow-lg animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-violet-100">Total Transactions</p>
                        <h3 class="text-2xl font-bold text-white mt-2">{{ number_format($totalTransactions, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="bg-violet-400/30 rounded-full p-3">
                        <i class="fas fa-exchange-alt text-2xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections détaillées -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Balance par Caisse -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInLeft">
                <h3 class="text-xl font-bold text-white mb-6">Balance par Caisse</h3>
                <div class="space-y-4">
                    @foreach($caisses as $caisse)
                    <div class="bg-white/5 rounded-lg p-4 transform hover:scale-102 transition-all duration-300">
                        <div class="flex justify-between items-center">
                            <span class="text-white">{{ $caisse->nom_caisse }}</span>
                            <span class="text-emerald-400 font-bold">{{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="mt-2 bg-white/20 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ ($caisse->balance_caisse / $totalBalance) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Balance par Produit -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInRight">
                <h3 class="text-xl font-bold text-white mb-6">Balance par Produit</h3>
                <div class="space-y-4">
                    @foreach($produits as $produit)
                    <div class="bg-white/5 rounded-lg p-4 transform hover:scale-102 transition-all duration-300">
                        <div class="flex justify-between items-center">
                            <span class="text-white">{{ $produit->nom_prod }}</span>
                            <span class="text-blue-400 font-bold">{{ number_format($produit->balance, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="mt-2 bg-white/20 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($produit->balance / $totalProduits) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

<!-- Totaux des Commissions par types de transactions -->
<div class="backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
    <h3 class="text-xl font-semibold text-purple-300 mb-4">Totaux des Commissions par types de transactions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($produits as $produit)
            @php
                // Nettoyer le nom du produit pour correspondre à la clé dans $commissionsParProduit
                $nomProduit = trim(str_replace(["\r\n", "\r", "\n"], '', $produit->nom_prod));
            @endphp
            <div class="backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20 hover:from-purple-900/30 hover:to-slate-900/30 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-gray-400 text-sm">{{ $produit->nom_prod }}</h4>
                        @if($nomProduit == 'FLOOZ')
                            <p class="text-lg font-bold text-purple-300 mt-1">
                                Commission Dépôt : {{ number_format($commissionsParProduit[$nomProduit]['commission_depot'] ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                            <p class="text-lg font-bold text-purple-300 mt-1">
                                Commission Retrait : {{ number_format($commissionsParProduit[$nomProduit]['commission_retrait'] ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        @else
                            <p class="text-lg font-bold text-purple-300 mt-1">
                                Dépôt : {{ number_format($commissionsParProduit[$nomProduit]['dépôt'] ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                            <p class="text-lg font-bold text-purple-300 mt-1">
                                Retrait : {{ number_format($commissionsParProduit[$nomProduit]['retrait'] ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        @endif
                    </div>
                    <div class="p-3 rounded-lg bg-gradient-to-br from-purple-500/10 to-pink-500/10">
                        <i class="fas fa-coins text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- Totaux des Commissions par Produit -->
<div class="backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
    <h3 class="text-xl font-semibold text-purple-300 mb-4">Totaux des Commissions par Produit</h3>
    <div id="commissions-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($commissionsParProduit as $produitNom => $commission)
            <div class="backdrop-blur-lg rounded-xl p-6 mb-4 border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
                <h4 class="text-gray-400 text-sm">{{ $produitNom }}</h4>
                <p class="text-lg font-bold text-purple-300 mt-1">
                    {{ number_format($commission['commission_totale'], 0, ',', ' ') }} FCFA
                </p>
            </div>
        @endforeach
    </div>
</div>
        <!-- Graphiques -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Graphique à barres -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInLeft">
        <h3 class="text-xl font-bold text-white mb-6">Transactions par Produit (Dépôt vs Retrait)</h3>
        <canvas id="barChart" class="w-full h-64"></canvas>
    </div>

    <!-- Graphique circulaire -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInRight">
        <h3 class="text-xl font-bold text-white mb-6">Répartition des Transactions par Produit</h3>
        <canvas id="pieChart" class="w-full h-64"></canvas>
    </div>
</div>

        <!-- Transactions par Type -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInUp">
            <h3 class="text-xl font-bold text-white mb-6">Transactions par Type</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($typeTransactions as $type_transaction)
                    <div class="bg-white/5 rounded-lg p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-white font-medium">
                                {{ $type_transaction->nom_type_transa ?? 'Type inconnu' }}
                            </span>
                            <div class="bg-indigo-500/30 rounded-full p-2">
                                <i class="fas fa-chart-line text-indigo-400"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-indigo-400">
                            {{ number_format($type_transaction->montant_total ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                @empty
                    <p class="text-white">Aucune transaction disponible pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Passer les données PHP à JavaScript
   
    const chartData = {
        labels: {!! json_encode($chartData['labels']) !!}, // Noms des produits
        depot: {!! json_encode($chartData['depot']) !!},   // Montants des dépôts par produit
        retrait: {!! json_encode($chartData['retrait']) !!} // Montants des retraits par produit
    };

    // Configuration des graphiques
    let barChart;
    let pieChart;

    // Fonction pour initialiser les graphiques
    function initCharts(data) {
        // Configuration du graphique à barres
        const barCtx = document.getElementById('barChart').getContext('2d');
        if (barChart) barChart.destroy();
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: data.labels, // Noms des produits
                datasets: [
                    {
                        label: 'Dépôt',
                        data: data.depot, // Montants des dépôts
                        backgroundColor: 'rgba(59, 130, 246, 0.8)', // Couleur pour les dépôts
                        borderWidth: 1
                    },
                    {
                        label: 'Retrait',
                        data: data.retrait, // Montants des retraits
                        backgroundColor: 'rgba(239, 68, 68, 0.8)', // Couleur pour les retraits
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white' // Couleur du texte de la légende
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white' // Couleur des ticks de l'axe Y
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Couleur de la grille de l'axe Y
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white' // Couleur des ticks de l'axe X
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Couleur de la grille de l'axe X
                        }
                    }
                }
            }
        });

        // Configuration du graphique circulaire
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        if (pieChart) pieChart.destroy();
        pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: data.labels, // Noms des produits
                datasets: [{
                    data: data.labels.map((label, index) => data.depot[index] + data.retrait[index]), // Montants totaux (dépôts + retraits)
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)', // Couleur pour le produit 1
                        'rgba(239, 68, 68, 0.8)',  // Couleur pour le produit 2
                        'rgba(16, 185, 129, 0.8)', // Couleur pour le produit 3
                        'rgba(139, 92, 246, 0.8)'  // Couleur pour le produit 4
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white' // Couleur du texte de la légende
                        }
                    }
                }
            }
        });
    }

    // Fonction pour mettre à jour les commissions
    function updateCommissions(commissionsData) {
        const commissionsContainer = document.getElementById('commissions-container');
        if (!commissionsContainer) return;

        // Vider le contenu existant
        commissionsContainer.innerHTML = '';

        // Ajouter les nouvelles données
      

for (const [produitId, commission] of Object.entries(commissionsData)) {
    const commissionElement = document.createElement('div');
    commissionElement.className = 'backdrop-blur-lg rounded-xl p-6 mb-4 border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20';

    // Accédez à la commission totale
    const totalCommission = parseFloat(commission.commission_totale);

    // Vérifiez si la commission est un nombre valide
    if (isNaN(totalCommission)) {
        console.error(`Commission invalide pour le produit ${produitId}:`, commission.commission_totale);
    }

    commissionElement.innerHTML = `
        <h4 class="text-gray-400 text-sm"> ${produitId}</h4>
        <p class="text-lg font-bold text-purple-300 mt-1">
            Total Commission: ${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalCommission)} FCFA
        </p>
    `;
    commissionsContainer.appendChild(commissionElement);
}
    }

    // Fonction pour mettre à jour les données
    function updateDashboard(period) {
        fetch(`/dashboard/filter?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Mise à jour des graphiques
                initCharts(data.chartData);

                // Mise à jour des statistiques
                document.querySelectorAll('[data-statistic]').forEach(element => {
                    const key = element.dataset.statistic;
                    if (data.statistics[key]) {
                        element.textContent = new Intl.NumberFormat('fr-FR').format(data.statistics[key]) + ' FCFA';
                    }
                });

                // Mise à jour des commissions
                if (data.commissionsParProduit) {
                    updateCommissions(data.commissionsParProduit);
                }
            });
    }

    // Écouteur d'événement pour le filtre
    document.getElementById('period-filter').addEventListener('change', function(e) {
        updateDashboard(e.target.value);
    });

    // Initialisation des graphiques au chargement
    document.addEventListener('DOMContentLoaded', function() {
        initCharts(chartData);

        // Initialisation des commissions si elles existent
        const initialCommissions = {!! json_encode($commissionsParProduit ?? []) !!};
        if (Object.keys(initialCommissions).length > 0) {
            updateCommissions(initialCommissions);
        }
    });

    // Animation au scroll
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate__animated').forEach((element) => {
        observer.observe(element);
    });
</script>
@endpush
@endsection