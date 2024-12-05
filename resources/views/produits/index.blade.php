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
                placeholder="Recherche global...">
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
@endpush

@push('scripts')
{{-- Scripts nécessaires --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        const table = $('#produits-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("produits.datatable") }}',
                type: 'GET',
                success: function(data) {
                    console.log('Data loaded:', data);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', xhr.responseText);
                    alert('Erreur de chargement des données');
                }
            },
            columns: [{
                    data: 'nom_prod',
                    name: 'nom_prod',
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'balance',
                    name: 'balance'
                },
                {
                    data: 'actif',
                    name: 'actif',
                    render: function(data) {
                        return data ?
                            '<span class="badge bg-success">Actif</span>' :
                            '<span class="badge bg-danger">Inactif</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    render: function(data, type, row) {
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
                search: "Recherche:",
                lengthMenu: "Afficher _MENU_ entrée",
                info: "Montrer _START_ to _END_ of _TOTAL_ entrée",
                infoEmpty: "Affichage de 0 à 0 de 0 entrées",
                infoFiltered: "(filtré à partir de _MAX_ nombre total d'entrées)",
                emptyTable: "Pas de données disponibles",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                }
            },
            search: true,
            ordering: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ]
        });

        // Recherche globale
        $('#global-search').on('keyup', function() {
            table.search($(this).val()).draw();
        });
    });
</script>
@endpush