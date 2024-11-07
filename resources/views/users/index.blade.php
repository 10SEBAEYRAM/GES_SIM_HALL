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

    {{-- Messages flash --}}
    @if(session()->has('success'))
        <div id="alert-success" 
             class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-md border border-green-300" 
             role="alert">
            {{ session('success') }}
        </div>
    @endif

   {{-- Messages flash --}}
@if(session()->has('success'))
    <div id="alert-success" 
         class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-md border border-green-300" 
         role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session()->has('error'))
    <div id="alert-error" 
         class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow-md border border-red-300" 
         role="alert">
        {{ session('error') }}
    </div>
@endif

{{-- Table des utilisateurs --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-300 mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                    Nom
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                    Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $user->nom_util }} {{ $user->prenom_util }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                        <div class="text-sm text-gray-500">
                            {{ $user->email_util }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $user->typeUser?->nom_type_users ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border-b border-gray-300">
                        <div class="flex space-x-3">
                            <a href="{{ route('users.edit', $user->id_util) }}" 
                               class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition duration-200 border border-blue-600">
                                Modifier
                            </a>
                            <form action="{{ route('users.destroy', $user->id_util) }}" 
                                  method="POST" 
                                  class="delete-user-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md transition duration-200 border border-red-600">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center border-b border-gray-300">
                        Aucun utilisateur trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination et Nombre total d'utilisateurs --}}
<div class="mt-8 border-t pt-4">
    <div class="flex justify-between items-center">
        {{ $users->links() }}
        <p class="text-gray-500">Nombre total d'utilisateurs : {{ $totalUsers }}</p>
    </div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des alertes
    const hideAlert = (alertId) => {
        const alert = document.getElementById(alertId);
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease-in-out';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        }
    };

    ['alert-success', 'alert-error'].forEach(hideAlert);

    // Confirmation de suppression
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
