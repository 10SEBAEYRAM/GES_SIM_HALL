@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        {{-- Header --}}
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Liste des produits</h2>
            <a href="{{ route('produits.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Nouveau produit
            </a>
        </div>

        {{-- Global Search --}}
        <div class="p-6">
            <input
                type="text"
                id="global-search"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Recherche globale...">
        </div>

        {{-- Products Table --}}
        <div class="overflow-x-auto">
            <table id="produits-table" class="w-full table-auto text-sm">
                <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Nom</th>
                        <th class="px-6 py-3 text-left">Balance</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Styles pour DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
{{-- Scripts nécessaires --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        const table = $('#produits-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("produits.datatable") }}',
                type: 'GET',
                data: function(d) {

                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', xhr.responseText);
                    alert('Erreur de chargement des données');
                }
            },
            columns: [{
                    data: 'nom_prod',
                    name: 'nom_prod'
                },
                {
                    data: 'balance',
                    name: 'balance',
                    render: $.fn.dataTable.render.number(',', '.', 2, '', ' FCFA')
                },
                {
                    data: 'actif',
                    name: 'actif',
                    render: function(data) {
                        return data ?
                            '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">Actif</span>' :
                            '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full">Inactif</span>';
                    }
                },
                {
                    data: 'id_prod',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `
                            <div class="flex justify-end space-x-2">
                                <a href="/produits/${data}/edit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-xs">
                                    Modifier
                                </a>
                                <form action="/produits/${data}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-xs" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                processing: "Chargement...",
                search: "Rechercher:",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                infoEmpty: "Aucun élément à afficher",
                infoFiltered: "(filtré de _MAX_ éléments)",
                infoPostFix: "",
                loadingRecords: "Chargement en cours...",
                zeroRecords: "Aucun élément correspondant",
                emptyTable: "Aucune donnée disponible",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Tous"]
            ]
        });

        // Recherche globale
        $('#global-search').on('keyup', function() {
            table.search($(this).val()).draw();
        });
    });
</script>
@endpush