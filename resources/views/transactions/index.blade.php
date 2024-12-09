@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-300 shadow-sm sm:rounded-lg p-6">

            {{-- En-tête avec Titre et Bouton --}}
            <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-300">
                <h2 class="text-2xl font-bold text-gray-800">Transactions</h2>
                <a href="{{ route('transactions.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 border border-blue-600 transition duration-200 font-semibold">
                    Nouvelle Transaction
                </a>
            </div>

            {{-- Affichage des messages de succès et d'erreur --}}
            <div class="flex-1 overflow-auto p-6 bg-gray-50">
                @if(session()->has('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                @if(session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    {{ session('error') }}
                </div>
                @endif
            </div>

            {{-- Affichage des Balances des Produits --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Balances des Produits</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($produits as $index => $produit)
                    <div class="p-4 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between"
                        style="background-color: {{ ['#e0f7fa', '#ffebee', '#fff3e0', '#e8f5e9'][$index % 4] }}">

                        <div>
                            <h4 class="text-base font-semibold text-gray-800">{{ $produit->nom_prod }}</h4>
                            <p class="text-sm text-gray-500">Balance Actuelle</p>
                        </div>

                        <div class="text-lg font-bold text-gray-900">
                            {{ number_format($produit->balance, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Affichage du solde de la caisse -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Solde des Caisses</h3>
                @foreach($caisses as $caisse)
                <div class="p-4 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between"
                    style="background-color: #f1f5f9">
                    <div>
                        <h4 class="text-base font-semibold text-gray-800">{{ $caisse->nom_caisse }}</h4>
                        <p class="text-sm text-gray-500">Solde actuel de la caisse</p>
                    </div>

                    <div class="text-lg font-bold text-gray-900">
                        {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Section Graphique des Totaux des Transactions -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Totaux des Transactions par Date</h3>
                <canvas id="transactionsChart" class="w-full h-96"></canvas>
            </div>

            {{-- Table des Transactions avec DataTables --}}
            <div class="mt-6">
                <table id="transactionsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bénéficiaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->typeTransaction->nom_type_transa }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->produit->nom_prod }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($transaction->commission_appliquee, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->num_beneficiaire }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($transaction->statut === 'COMPLETE') bg-green-100 text-green-800
                                    @elseif($transaction->statut === 'ANNULE') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $transaction->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('transactions.show', $transaction->id_transaction) }}"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-blue-700 mr-2">
                                    Détails
                                </a>

                                <form action="{{ route('transactions.destroy', $transaction->id_transaction) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-red-700"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Aucune transaction enregistrée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<!-- Inclusion de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.colVis.min.js"></script>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#transactionsTable')) {
            $('#transactionsTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['csv', 'excel', 'pdf', 'print', 'colvis'],
                language: {
                    processing: "Traitement en cours...",
                    search: "Rechercher&nbsp;:",
                    lengthMenu: "Afficher _MENU_ éléments",
                    info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                    infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                    infoFiltered: "(filtré à partir de _MAX_ éléments au total)",
                    loadingRecords: "Chargement en cours...",
                    zeroRecords: "Aucun élément à afficher",
                    emptyTable: "Aucune donnée disponible dans le tableau",
                    paginate: {
                        first: "Premier",
                        previous: "Précédent",
                        next: "Suivant",
                        last: "Dernier"
                    }
                }
            });
        }

        // Création du graphique des transactions
        var ctx = document.getElementById('transactionsChart').getContext('2d');
        var transactionsChart = new Chart(ctx, {
            type: 'line', // Type de graphique (ligne)
            data: {
                labels: @json($data['labels']), // Dates
                datasets: [{
                    label: 'Total des Transactions (FCFA)',
                    data: @json($data['totals']), // Totaux
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Total : ' + new Intl.NumberFormat().format(tooltipItem.raw) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000, // Durée de l'animation
                    easing: 'easeInOutQuad' // Type d'animation
                }
            }
        });
    });
</script>

@endpush

@endsection