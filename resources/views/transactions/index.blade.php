@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInDown border border-purple-500/10 bg-gradient-to-r from-purple-900/30 to-slate-900/30">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600 mb-2">Transactions</h1>
                    <p class="text-gray-400">Gestion des transactions</p>
                </div>
                <a href="{{ route('transactions.create') }}"
                    class="relative inline-flex items-center px-6 py-3 overflow-hidden rounded-lg bg-gradient-to-r from-purple-500/10 to-pink-500/10 hover:from-purple-500/20 hover:to-pink-500/20 border border-purple-500/20 transition-all duration-300 hover:scale-105 hover:shadow-lg group">
                    <span class="relative flex items-center space-x-3 text-purple-300">
                        <i class="fas fa-plus"></i>
                        <span>Nouvelle Transaction</span>
                    </span>
                </a>
            </div>
        </div>

        <!-- Messages de feedback -->
        @if(session('success'))
        <div class="backdrop-blur-lg border-l-4 border-emerald-500 px-6 py-4 rounded-lg mb-6 animate__animated animate__fadeInDown bg-emerald-500/5">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-400 mr-3"></i>
                <span class="text-emerald-300">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="backdrop-blur-lg border-l-4 border-red-500 px-6 py-4 rounded-lg mb-6 animate__animated animate__fadeInDown bg-red-500/5">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                <span class="text-red-300">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Balances des Produits -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($produits as $produit)
            <div class="backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20 hover:from-purple-900/30 hover:to-slate-900/30 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-gray-400 text-sm">{{ $produit->nom_prod }}</h4>
                        <p class="text-2xl font-bold text-purple-300 mt-1">
                            {{ number_format($produit->balance, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-gradient-to-br from-purple-500/10 to-pink-500/10">
                        <i class="fas fa-wallet text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
                <!-- Solde des Caisses -->
                <div class="backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
            <h3 class="text-xl font-semibold text-purple-300 mb-4">Solde des Caisses</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($caisses as $caisse)
                <div class="rounded-lg p-4 border border-purple-500/10 bg-gradient-to-br from-slate-900/40 to-purple-900/40 hover:from-slate-900/50 hover:to-purple-900/50 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-gray-400">{{ $caisse->nom_caisse }}</h4>
                            <p class="text-xl font-bold text-purple-300 mt-1">
                                {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-gradient-to-br from-purple-500/10 to-indigo-500/10">
                            <i class="fas fa-cash-register text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Graphique -->
        <div class="backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
    <h3 class="text-xl font-semibold text-purple-300 mb-4">Totaux des Transactions par Date</h3>
    <!-- Modification de la hauteur de h-96 à h-64 -->
    <canvas id="transactionsChart" class="w-full h-64"></canvas>
</div>

        <!-- Table des Transactions -->
        <div class="backdrop-blur-lg rounded-xl overflow-hidden shadow-lg animate__animated animate__fadeInUp border border-purple-500/10 bg-gradient-to-br from-purple-900/20 to-slate-900/20">
            <div class="p-6">
                <table id="transactionsTable" class="w-full">
                    <thead>
                        <tr class="border-b border-purple-500/10">
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Date/Heure</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Produit</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Montant</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Commission</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Bénéficiaire</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-purple-300">Statut</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-purple-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-500/10">
                        @foreach($transactions as $transaction)
                        <tr class="hover:bg-purple-900/10 transition-colors duration-200">
                            <td class="px-6 py-4 text-gray-300">
                                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($transaction->typeTransaction->nom_type_transa, 0, 2)) }}
                                    </div>
                                    <span class="text-gray-300">{{ $transaction->typeTransaction->nom_type_transa }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ $transaction->produit->nom_prod }}
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ number_format($transaction->commission_grille_tarifaire, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-gray-300">
                                {{ $transaction->num_beneficiaire }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($transaction->statut === 'COMPLETE') bg-emerald-500/10 text-emerald-300 border border-emerald-500/30
                                    @elseif($transaction->statut === 'ANNULE') bg-red-500/10 text-red-300 border border-red-500/30
                                    @else bg-yellow-500/10 text-yellow-300 border border-yellow-500/30 
                                    @endif">
                                    {{ $transaction->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-3 justify-end">
                                    <a href="{{ route('transactions.show', $transaction->id_transaction) }}"
                                        class="bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-blue-500/30">
                                        <i class="fas fa-eye"></i>
                                        <span>Détails</span>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $transaction->id_transaction) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')"
                                            class="bg-red-500/10 hover:bg-red-500/20 text-red-300 px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-red-500/30">
                                            <i class="fas fa-trash-alt"></i>
                                            <span>Supprimer</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    /* Reset DataTables Background */
    .dataTables_wrapper,
    .dataTables_wrapper * {
        background: transparent !important;
    }

    /* Wrapper Styles */
    .dataTables_wrapper {
        padding: 1rem 0;
        color: rgb(216, 180, 254); /* text-purple-300 */
    }

    /* Search and Length Inputs */
    .dataTables_filter input,
    .dataTables_length select {
        background: rgba(147, 51, 234, 0.1) !important; /* bg-purple-600/10 */
        border: 1px solid rgba(147, 51, 234, 0.2) !important;
        color: rgb(216, 180, 254) !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem !important;
        outline: none !important;
    }

    .dataTables_filter input:focus,
    .dataTables_length select:focus {
        border-color: rgba(147, 51, 234, 0.3) !important;
        box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.1) !important;
    }

    /* Select Options */
    .dataTables_length select option {
        background: rgb(15, 23, 42) !important; /* bg-slate-900 */
        color: rgb(216, 180, 254) !important;
    }

    /* Export Buttons */
    .dt-buttons {
        margin-bottom: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    .dt-button {
        background: rgba(147, 51, 234, 0.1) !important;
        border: 1px solid rgba(147, 51, 234, 0.2) !important;
        color: rgb(216, 180, 254) !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem !important;
        transition: all 0.3s ease !important;
    }

    .dt-button:hover {
        background: rgba(147, 51, 234, 0.2) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                   0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Pagination */
    .dataTables_paginate {
        margin-top: 1rem;
    }

    .paginate_button {
        padding: 0.5rem 1rem !important;
        margin: 0 0.25rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid rgba(147, 51, 234, 0.2) !important;
        color: rgb(216, 180, 254) !important;
        transition: all 0.3s ease !important;
    }

    .paginate_button:hover:not(.disabled) {
        background: rgba(147, 51, 234, 0.2) !important;
        border-color: rgba(147, 51, 234, 0.3) !important;
    }

    .paginate_button.current {
        background: rgba(147, 51, 234, 0.2) !important;
        border-color: rgba(147, 51, 234, 0.3) !important;
    }

    .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Info Text */
    .dataTables_info {
        color: rgb(216, 180, 254);
        opacity: 0.8;
    }

    /* Table Cells */
    table.dataTable tbody td {
        padding: 1rem !important;
        vertical-align: middle !important;
    }

    /* Hover Effect */
    table.dataTable tbody tr:hover {
        background: rgba(147, 51, 234, 0.1) !important;
    }

    /* Remove Default Borders */
    table.dataTable,
    table.dataTable th,
    table.dataTable td {
        border: none !important;
    }

    /* Custom Scrollbar */
    .dataTables_scrollBody::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-track {
        background: rgba(147, 51, 234, 0.1);
        border-radius: 4px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: rgba(147, 51, 234, 0.2);
        border-radius: 4px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
        background: rgba(147, 51, 234, 0.3);
    }
</style>
@endpush
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Configuration de DataTables avec thème sombre
    var table = $('#transactionsTable').DataTable({
        dom: '<"top"Bf>rt<"bottom"lip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-2"></i>Excel',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                title: 'Transactions - Export Excel'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-2"></i>PDF',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                title: 'Transactions - Export PDF',
                customize: function(doc) {
                    // Personnalisation du PDF pour le thème sombre
                    doc.styles.tableHeader.color = '#D8B4FE';
                    doc.styles.tableBody.color = '#D8B4FE';
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-2"></i>Imprimer',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                title: 'Transactions'
            }
        ],
        language: {
            processing: "Traitement en cours...",
            search: "Rechercher :",
            lengthMenu: "Afficher _MENU_ éléments",
            info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
            infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
            infoFiltered: "(filtré de _MAX_ éléments au total)",
            loadingRecords: "Chargement en cours...",
            zeroRecords: '<span class="text-purple-300">Aucun élément à afficher</span>',
            emptyTable: '<span class="text-purple-300">Aucune donnée disponible</span>',
            paginate: {
                first: "Premier",
                previous: "Précédent",
                next: "Suivant",
                last: "Dernier"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tout"]],
        order: [[0, 'desc']],
        responsive: true,
        drawCallback: function() {
            // Réappliquer les styles après chaque redraw
            applyCustomStyles();
        }
    });

    // Configuration du graphique avec thème sombre
    var ctx = document.getElementById('transactionsChart').getContext('2d');
    var transactionsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($data['labels']),
            datasets: [{
                label: 'Total des Transactions (FCFA)',
                data: @json($data['totals']),
                backgroundColor: 'rgba(147, 51, 234, 0.2)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(147, 51, 234, 1)',
                pointBorderColor: '#1E1B4B',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: 'rgba(147, 51, 234, 1)',
                pointHoverBorderColor: '#1E1B4B'
            }]
        },
        options: {
    responsive: true,
    maintainAspectRatio: false,
    aspectRatio: 2,
    interaction: {
        intersect: false,
        mode: 'index'
    },
    plugins: {
        legend: {
            position: 'top',
            labels: {
                padding: 15,
                boxWidth: 10,
                color: 'rgba(216, 180, 254, 0.8)',
                font: {
                    size: 11,
                    family: "'Inter', sans-serif"
                }
            }
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#D8B4FE',
            bodyColor: '#D8B4FE',
            borderColor: 'rgba(147, 51, 234, 0.2)',
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
                label: function(context) {
                    return 'Total : ' + new Intl.NumberFormat('fr-FR').format(context.raw) + ' FCFA';
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(147, 51, 234, 0.1)',
                drawBorder: false
            },
            ticks: {
                color: 'rgba(216, 180, 254, 0.8)',
                font: {
                    size: 11,
                    family: "'Inter', sans-serif"
                },
                callback: function(value) {
                    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                }
            }
        },
        x: {
            grid: {
                color: 'rgba(147, 51, 234, 0.1)',
                drawBorder: false
            },
            ticks: {
                color: 'rgba(216, 180, 254, 0.8)',
                font: {
                    size: 11,
                    family: "'Inter', sans-serif"
                }
            }
        }
    }
}
    });

    // Fonction pour appliquer les styles personnalisés
    function applyCustomStyles() {
        // Styles pour les boutons d'export
        $('.dt-buttons .dt-button').addClass('hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300');
        
        // Styles pour les inputs et selects
        $('.dataTables_filter input, .dataTables_length select').addClass('focus:ring-2 ring-purple-500/20 transition-all duration-300');
        
        // Assurer la cohérence des couleurs
        $('#transactionsTable_wrapper').find('*:not(select option)').css('color', 'rgb(216, 180, 254)');
        $('select option').css('color', 'rgb(15, 23, 42)');
    }

    // Appliquer les styles initiaux
    applyCustomStyles();

    // Réappliquer les styles lors du redimensionnement
    $(window).resize(function() {
        applyCustomStyles();
    });
});
</script>
@endpush
@endsection