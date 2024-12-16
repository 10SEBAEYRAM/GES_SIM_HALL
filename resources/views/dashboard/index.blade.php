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
      <!-- Graphiques -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Graphique à barres -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInLeft">
                <h3 class="text-xl font-bold text-white mb-6">Transactions par Type</h3>
                <canvas id="barChart" class="w-full h-64"></canvas>
            </div>

            <!-- Graphique circulaire -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInRight">
                <h3 class="text-xl font-bold text-white mb-6">Répartition des Transactions</h3>
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
                labels: data.labels,
                datasets: [{
                    label: 'Montant des transactions',
                    data: data.values,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
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
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        });
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
            });
    }

    // Écouteur d'événement pour le filtre
    document.getElementById('period-filter').addEventListener('change', function(e) {
        updateDashboard(e.target.value);
    });

    // Initialisation des graphiques au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const initialData = {
            labels: {!! json_encode($chartData['labels']) !!},
            values: {!! json_encode($chartData['values']) !!}
        };
        initCharts(initialData);
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