@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec effet glassmorphism -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInDown">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Produits</h1>
                    <p class="text-gray-300">Gestion des produits</p>
                </div>
                <a href="{{ route('produits.create') }}"
                    class="bg-blue-600/20 hover:bg-blue-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-blue-500/30">
                    <i class="fas fa-plus"></i>
                    <span>Nouveau Produit</span>
                </a>
            </div>
        </div>

        <!-- Messages de feedback -->
        @if(session('success'))
        <div class="bg-emerald-500/20 backdrop-blur-lg border-l-4 border-emerald-500 text-white px-6 py-4 rounded-lg mb-6 animate__animated animate__fadeInDown">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-400 mr-3"></i>
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-500/20 backdrop-blur-lg border-l-4 border-red-500 text-white px-6 py-4 rounded-lg mb-6 animate__animated animate__fadeInDown">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                {{ session('error') }}
            </div>
        </div>
        @endif

        <!-- Table -->
        <!-- Table -->
<div class="bg-white/10 backdrop-blur-lg rounded-xl overflow-hidden shadow-xl animate__animated animate__fadeInUp">
    <div class="p-6">
        <table id="produitsTable" class="w-full">
            <thead>
                <tr class="border-b border-indigo-500/30">
                    <th class="px-6 py-3 text-left text-sm font-semibold text-white">Nom</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-white">Balance</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-white">Status</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($produits as $produit)
                <tr class="hover:bg-white/5 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($produit->nom, 0, 1)) }}
                            </div>
                            <span class="text-white">{{ $produit->nom_prod }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-300">{{ number_format($produit->balance, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $produit->status ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300' }}">
                            {{ $produit->status ? 'Actif' : 'Actif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex gap-3 justify-end">
                              <a href="{{ route('produits.edit', $produit->id_prod) }}"
                                class="bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-blue-300">
                                <i class="fas fa-edit"></i>
                                <span>Modifier</span>
                            </a>
                            <form action="{{ route('produits.destroy', $produit->id_prod) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
                                    class="bg-red-600/20 hover:bg-red-600/30 text-red-300 px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-red-300">
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
    /* Styles spécifiques pour le message "Aucun élément à afficher" */
    .dataTables_empty,
    table.dataTable tbody tr.odd td.dataTables_empty,
    table.dataTable tbody tr.even td.dataTables_empty,
    .dataTables_wrapper .dataTables_empty {
        color: white !important;
        background: transparent !important;
        background-color: transparent !important;
    }

    /* Force la couleur blanche sur toutes les cellules du tableau */
    table.dataTable tbody td,
    table.dataTable tbody tr td {
        color: white !important;
    }

    /* Reset complet pour DataTables */
    .dataTables_wrapper,
    .dataTables_wrapper * {
        background: transparent !important;
    }

    /* Inputs et Selects */
    .dataTables_filter input,
    .dataTables_length select,
    select[name="produitsTable_length"] {
        background: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem 1rem !important;
        color: white !important;
    }

    /* Couleur du texte des options dans les selects */
    .dataTables_length select option {
        background: #1F2937 !important;
        color: black !important;
    }

    /* Table */
    table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    table.dataTable.no-footer {
        border-bottom: none !important;
    }

    /* Boutons d'export */
    .dt-button {
        background: rgba(59, 130, 246, 0.2) !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
        color: white !important;
        margin: 0.25rem !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem !important;
    }

    .dt-button:hover {
        background: rgba(59, 130, 246, 0.3) !important;
        transform: translateY(-1px);
    }

    /* Info et Length menu */
    .dataTables_info {
        color: white !important;
    }

    .dataTables_length label,
    .dataTables_filter label {
        color: white !important;
    }

    /* Styles spécifiques pour la pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        color: white !important;
        background: transparent !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }

    /* Style pour les boutons désactivés */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        color: rgba(255, 255, 255, 0.5) !important;
        background: transparent !important;
        border: none !important;
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

<script>
$(document).ready(function() {
    var table = $('#produitsTable').DataTable({
        dom: '<"top"Bf>rt<"bottom"lip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-2"></i>Excel',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1]
                },
                title: 'Produits - Export Excel'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-2"></i>PDF',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1]
                },
                title: 'Produits - Export PDF'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-2"></i>Imprimer',
                className: 'dt-button',
                exportOptions: {
                    columns: [0, 1]
                },
                title: 'Produits'
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
            zeroRecords: '<span style="color: white !important;">Aucun élément à afficher</span>',
            emptyTable: '<span style="color: white !important;">Aucune donnée disponible dans le tableau</span>',
            paginate: {
                first: "Premier",
                previous: "Précédent",
                next: "Suivant",
                last: "Dernier"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tout"]],
        order: [[0, 'asc']],
        responsive: true,
        initComplete: function(settings, json) {
            applyCustomStyles();
            
            const observer = new MutationObserver(function(mutations) {
                applyCustomStyles();
            });

            observer.observe(document.querySelector('#produitsTable_wrapper'), {
                childList: true,
                subtree: true
            });
        }
    });

    function applyCustomStyles() {
        // Styles des boutons
        $('.dt-buttons .dt-button').addClass('bg-blue-600/20 hover:bg-blue-600/30 border-blue-500/30');
        
        // Styles des inputs
        $('.dataTables_filter input, .dataTables_length select').addClass('bg-white/10 border-white/20');
        
        // Force les couleurs
        $('#produitsTable_wrapper').find('*:not(select option)').css('color', 'white');
        
        // Style pour les options des selects
        $('select option').css('color', 'black');
        
        // Reset des backgrounds
        $('.dataTables_wrapper *').css('background', 'transparent');
        
        // Force la couleur blanche pour tous les boutons de pagination
        $('.dataTables_paginate .paginate_button').each(function() {
            if (!$(this).hasClass('disabled')) {
                $(this).css({
                    'color': 'white !important',
                    'background': 'transparent !important',
                    'border': 'none !important'
                });
            }
        });

        // Style spécifique pour Previous/Next
        $('.dataTables_paginate .previous, .dataTables_paginate .next').css({
            'color': 'white !important',
            'background': 'transparent !important',
            'border': 'none !important'
        });

        // Style pour les boutons désactivés
        $('.dataTables_paginate .paginate_button.disabled').css({
            'color': 'rgba(255, 255, 255, 0.5) !important',
            'background': 'transparent !important'
        });
    }

    // Appliquer les styles après un court délai
    setTimeout(applyCustomStyles, 100);

    // Gestionnaire d'événements pour le redraw
    table.on('draw.dt', function() {
        applyCustomStyles();
    });
});

// Fonction pour gérer les autorisations
function handleUnauthorized(hasPermission, action) {
    if (hasPermission === 'false') {
        alert(`Vous n'êtes pas autorisé à ${action}.`);
        return false;
    }
    return true;
}
</script>
@endpush
@endsection