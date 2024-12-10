@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-8 py-6 bg-white shadow-lg rounded-lg border border-gray-300">
    {{-- En-tête avec Titre et Bouton --}}
    <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-300">
        <h1 class="text-2xl font-bold text-gray-800">Liste des Utilisateurs</h1>
        <a href="{{ route('users.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition duration-300 ease-in-out border border-blue-600 font-semibold">
            Nouvel Utilisateur
        </a>
    </div>

    {{-- Table des utilisateurs --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-300">
        <table class="min-w-full divide-y divide-gray-200" id="users-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                        {{ $user->nom_util }} {{ $user->prenom_util }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->email_util }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @php
                        $typeClasses = [
                        'Admin' => 'bg-red-100 text-red-800',
                        'Utilisateur' => 'bg-blue-100 text-blue-800',
                        'Invité' => 'bg-green-100 text-green-800',
                        ];
                        $typeClass = $typeClasses[$user->typeUser?->nom_type_users] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClass }}">
                            {{ $user->typeUser?->nom_type_users ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-3">
                            <a href="{{ route('users.edit', $user->id_util) }}"
                                class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-md transition duration-300">
                                Modifier
                            </a>
                            <form action="{{ route('users.destroy', $user->id_util) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded-md transition duration-300"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
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

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: 'Exporter Excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'csv',
                    text: 'Exporter CSV',
                    className: 'btn btn-secondary'
                },
                {
                    extend: 'pdf',
                    text: 'Exporter PDF',
                    className: 'btn btn-danger'
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    className: 'btn btn-info'
                },
            ],
            language: {
                processing: "Traitement en cours...",
                search: "Rechercher&nbsp;:",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                infoEmpty: "Affichage de 0 à 0 sur 0 élément",
                infoFiltered: "(filtré de _MAX_ éléments au total)",
                loadingRecords: "Chargement...",
                zeroRecords: "Aucun résultat trouvé",
                emptyTable: "Aucune donnée disponible",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                }
            }
        });
    });
</script>
@endpush
@endsection