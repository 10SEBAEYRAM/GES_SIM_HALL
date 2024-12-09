@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-blue-50 via-white to-gray-50 overflow-hidden shadow-lg sm:rounded-lg p-6 border border-gray-300">
            <div class="flex justify-between items-center mb-6 border-b border-gray-300 pb-4">
                <h2 class="text-3xl font-bold text-gray-700">Produits</h2>
                <a href="{{ route('produits.create') }}"
                    class="bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg shadow-md transform transition duration-300 hover:scale-105 border border-blue-700">
                    Nouveau Produit
                </a>
            </div>

            {{-- Messages de feedback --}}
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-md mb-4 shadow-md border border-green-400">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-md mb-4 shadow-md border border-red-400">
                {{ session('error') }}
            </div>
            @endif

            <!-- Zone de filtrage -->
            <div class="mb-4 flex gap-4 animate-fade-in-down">
                <select id="filterStatut" class="border-gray-300 rounded-lg p-3 shadow-md">
                    <option value="">Tous les statuts</option>
                    <option value="Actif">Actif</option>
                    <option value="Inactif">Inactif</option>
                </select>

                <input type="number" id="filterMinBalance" class="border-gray-300 rounded-lg p-3 shadow-md" placeholder="Balance Min">
                <input type="number" id="filterMaxBalance" class="border-gray-300 rounded-lg p-3 shadow-md" placeholder="Balance Max">
                <input type="text" id="filterNom" class="border-gray-300 rounded-lg p-3 shadow-md" placeholder="Nom du Produit">
            </div>

            <!-- Tableau -->
            <table id="produitsTable" class="min-w-full divide-y divide-gray-200 bg-white shadow-lg rounded-lg">
                <thead class="bg-gradient-to-r from-gray-100 via-gray-200 to-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nom
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Balance
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produits as $produit)
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $produit->nom_prod }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($produit->balance, 2, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $produit->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $produit->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex gap-3 justify-end">
                                <a href="{{ route('produits.edit', $produit->id_prod) }}"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-blue-700">
                                    Modifier
                                </a>
                                <form action="{{ route('produits.destroy', $produit->id_prod) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 px-4 py-2 rounded-md shadow transform transition duration-200 hover:scale-105 border border-red-700"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
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

<style>
    @import url('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
    @import url('https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css');

    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

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
        var table = $('#produitsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    text: 'Copier',
                    exportOptions: {
                        columns: [0, 1, 2] // Index des colonnes à inclure dans l'export
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

        $('#filterStatut').on('change', function() {
            table.column(2).search(this.value).draw();
        });

        $('#filterMinBalance').on('input', function() {
            $.fn.dataTable.ext.search.push(
                function(settings, data) {
                    var min = parseFloat($('#filterMinBalance').val()) || 0;
                    var balance = parseFloat(data[1].replace(/\s/g, '').replace('FCFA', '')) || 0;
                    return balance >= min;
                }
            );
            table.draw();
        });

        $('#filterMaxBalance').on('input', function() {
            $.fn.dataTable.ext.search.push(
                function(settings, data) {
                    var max = parseFloat($('#filterMaxBalance').val()) || Infinity;
                    var balance = parseFloat(data[1].replace(/\s/g, '').replace('FCFA', '')) || 0;
                    return balance <= max;
                }
            );
            table.draw();
        });

        $('#filterNom').on('input', function() {
            table.column(0).search(this.value).draw();
        });
    });
</script>
@endpush

@endsection