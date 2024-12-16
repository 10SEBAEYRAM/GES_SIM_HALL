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
{{-- Par celle-ci --}}
{{-- Dans resources/views/caisses/index.blade.php --}}
<a href="{{ route('caisses.mouvements.create') }}"
    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md border border-green-700 transition duration-300 ease-in-out">
    Ajouter un Mouvement
</a>
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
            @if (session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md border border-green-300">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md border border-red-300">
                {{ session('error') }}
            </div>
            @endif

            {{-- Filtres --}}
            <div class="flex space-x-4 mb-6">
                <div>
                    <label for="date_start" class="text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" id="date_start"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="date_end" class="text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" id="date_end"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="min_balance" class="text-sm font-medium text-gray-700">Montant minimum</label>
                    <input type="number" id="min_balance"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="any">
                </div>
                <div>
                    <label for="max_balance" class="text-sm font-medium text-gray-700">Montant maximum</label>
                    <input type="number" id="max_balance"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="any">
                </div>
            </div>

            {{-- Tableau --}}
            <table id="caissesTable" class="min-w-full divide-y divide-gray-200 bg-white shadow-lg rounded-lg">
                <thead class="bg-gradient-to-r from-gray-100 via-gray-200 to-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom
                            de la Caisse</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($caisses as $caisse)
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $caisse->nom_caisse }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} XOF
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $caisse->created_at->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex gap-3 justify-end">
                                {{-- Bouton Voir détails --}}
                                <a href="{{ route('caisses.show', $caisse->id_caisse) }}"
                                    class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white hover:from-indigo-600 hover:to-indigo-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-indigo-700">
                                    Voir détails
                                </a>

                                {{-- Bouton Modifier (existant) --}}
                                <a href="{{ route('caisses.edit', $caisse->id_caisse) }}"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-blue-700">
                                    Modifier
                                </a>

                                {{-- Bouton Supprimer (existant) --}}
                                <form action="{{ route('caisses.destroy', $caisse->id_caisse) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-red-700"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                        Supprimer
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

<!-- Ajouter les fichiers CSS et JS directement -->
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {

        var table = $('#caissesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    text: 'Copier',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                }
            ],
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

        // Filtrage
        $('#date_start, #date_end, #min_balance, #max_balance').on('input change', function() {
            table.draw();
        });

    });
</script>
@endpush

@endsection