{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Liste des Utilisateurs</h1>
        <a href="{{ route('users.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
            Nouvel Utilisateur
        </a>
    </div>

    {{-- Messages flash --}}
    @if(session()->has('success'))
        <div id="alert-success" 
             class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" 
             role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div id="alert-error" 
             class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" 
             role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table des utilisateurs --}}
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nom
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $user->nom_util }} {{ $user->prenom_util }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ $user->email_util }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $user->typeUser?->nom_type_users ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                <a href="{{ route('users.edit', $user->id_util) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 transition duration-200">
                                    Modifier
                                </a>
                                <form action="{{ route('users.destroy', $user->id_util) }}" 
                                      method="POST" 
                                      class="delete-user-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition duration-200">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
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

@push('styles')
<style>
/* On garde uniquement les styles personnalisés qui ne sont pas déjà couverts par Tailwind */
.alert {
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

/* Les autres styles sont gérés par les classes Tailwind */
</style>
@endpush
@endsection