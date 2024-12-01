@extends('layouts.app')

@section('title', 'Liste des Caisses')

@section('head')
<!-- Importation des fichiers CSS pour DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">

            {{-- Titre et bouton Créer --}}
            <div class="flex justify-between items-center mb-8 border-b pb-4 border-gray-300">
                <h2 class="text-2xl font-bold text-gray-800">Liste des Caisses</h2>
                <a href="{{ route('caisses.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md border border-blue-700 transition duration-300 ease-in-out">
                    Créer une nouvelle Caisse
                </a>
            </div>

            {{-- Total Balance des caisses --}}
            <div class="mb-6">
                <span class="text-lg font-semibold">Total des balances :</span>
                <span class="text-xl font-bold text-gray-800">
                    {{ number_format($total_balance, 0, ',', ' ') }} XOF
                </span>
            </div>


            {{-- Messages flash --}}
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md border border-green-300">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md border border-red-300">
                {{ session('error') }}
            </div>
            @endif

            {{-- Filtres --}}
            <div class="flex space-x-4 mb-6">
                <div>
                    <label for="date_start" class="text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" id="date_start" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="date_end" class="text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" id="date_end" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="min_balance" class="text-sm font-medium text-gray-700">Montant minimum</label>
                    <input type="number" id="min_balance" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="any">
                </div>
                <div>
                    <label for="max_balance" class="text-sm font-medium text-gray-700">Montant maximum</label>
                    <input type="number" id="max_balance" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="any">
                </div>
                <button id="apply_filters" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md border border-blue-700">
                    Appliquer les filtres
                </button>
            </div>


            {{-- Tableau des caisses --}}
            <table id="caisses_table" class="min-w-full divide-y divide-gray-200 mb-8 bg-white border border-gray-300 rounded-lg overflow-hidden shadow-md">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nom de la Caisse
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Balance
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date de création
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dernière modification
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($caisses as $caisse)
                    <tr class="hover:bg-gray-50 transition duration-200 border-b border-gray-300">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            <input type="checkbox" class="select_row" value="{{ $caisse->id_caisse }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ $caisse->id_caisse }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ $caisse->nom_caisse }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ number_format($caisse->balance_caisse, 2, ',', ' ') }} XOF
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ $caisse->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ $caisse->updated_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            {{-- Lien pour modifier --}}
                            <a href="{{ route('caisses.edit', $caisse->id_caisse) }}"
                                class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition duration-200 border border-blue-700 mr-3">
                                Modifier
                            </a>

                            {{-- Formulaire pour supprimer --}}
                            <form action="{{ route('caisses.destroy', $caisse->id_caisse) }}"
                                method="POST"
                                class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md transition duration-200 border border-red-700"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

            {{-- Pagination --}}
            <div class="mt-8 border-t pt-4 border-gray-300">
                <div class="flex justify-between items-center">
                    {{ $caisses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal d'historique --}}
<div id="history_modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold text-gray-800">Historique des modifications</h3>
        <div id="history_content" class="mt-4 text-gray-700"></div>
        <button id="close_modal" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-md">
            Fermer
        </button>
    </div>
</div>

@endsection

@section('scripts')

<script>
    import $ from 'jquery';
    import 'datatables.net';
    import 'datatables.net-buttons';
    import 'datatables.net-buttons/js/buttons.html5.js';
    import 'datatables.net-buttons/js/buttons.print.js';
    import moment from 'moment';

    $(document).ready(function() {
        const number_format = (value, decimals = 2) => {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(value);
        };

        const table = $('#caisses_table').DataTable({
            responsive: true,
            pagingType: 'full_numbers',
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: 'Exporter en Excel',
                    className: 'bg-green-500 text-white px-4 py-2 rounded-md'
                },
                {
                    extend: 'pdf',
                    text: 'Exporter en PDF',
                    className: 'bg-red-500 text-white px-4 py-2 rounded-md'
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    className: 'bg-gray-500 text-white px-4 py-2 rounded-md'
                }
            ],
            language: {
                processing: "Traitement en cours...",
                search: "Rechercher&nbsp;:",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                infoFiltered: "(filtré de _MAX_ éléments au total)",
                loadingRecords: "Chargement en cours...",
                zeroRecords: "Aucun élément à afficher",
                emptyTable: "Aucune donnée disponible dans le tableau",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            },
            columnDefs: [{
                    targets: 0,
                    orderable: false,
                    render: function() {
                        return `<input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">`;
                    }
                },
                {
                    targets: 2,
                    render: function(data) {
                        const numericValue = parseFloat(data);
                        const formattedValue = number_format(numericValue, 2);
                        const colorClass = numericValue >= 0 ? 'text-green-600' : 'text-red-600';
                        return `<span class="${colorClass} font-bold">${formattedValue} XOF</span>`;
                    }
                },
                {
                    targets: [3, 4],
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY HH:mm') : 'Non spécifié';
                    }
                }
            ]
        });

        $('#apply_filters').on('click', function() {
            table.draw();
        });
    });
</script>

@endsection