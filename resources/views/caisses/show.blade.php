@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- En-tête -->
            <div class="p-6 sm:p-8 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Détails de la Caisse</h2>
                    <a href="{{ route('caisses.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Corps -->
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate__animated animate__fadeIn">
                    <!-- Informations de base -->
                    <div class="bg-yellow-100 rounded-xl p-6 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nom de la Caisse</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ $caisse->nom_caisse ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Date de création</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ $caisse->created_at ? $caisse->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Solde actuel -->
                    <div class="bg-blue-100 rounded-xl p-6 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Solde Actuel</h3>
                            <p class="mt-2 text-lg font-bold text-gray-900">
                                {{ number_format($caisse->balance_caisse ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        @if(isset($mouvements) && $mouvements->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Solde Avant</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ number_format($mouvements->first()->solde_avant ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>

                        @endif
                    </div>
                    <!-- Totaux -->
                    <div class="bg-green-100 rounded-xl p-6 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Emprunts</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ number_format($caisse->total_emprunts ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Remboursements</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ number_format($caisse->total_remboursements ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Retraits</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">
                                {{ number_format($caisse->total_retraits ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Liste des mouvements -->
                @if(isset($mouvements) && $mouvements->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des mouvements</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Initial</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Remboursé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Restant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opérateur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mouvements as $mouvement)
                                <tr class="{{ in_array($mouvement->type_mouvement, ['emprunt', 'pret']) ? 'bg-yellow-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $mouvement->created_at ? $mouvement->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ ucfirst($mouvement->type_mouvement ?? 'N/A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(in_array($mouvement->type_mouvement, ['emprunt', 'pret']))
                                            {{ number_format($mouvement->montant ?? 0, 0, ',', ' ') }} FCFA
                                        @elseif($mouvement->type_mouvement === 'remboursement')
                                            {{ number_format($mouvement->mouvementReference->montant ?? 0, 0, ',', ' ') }} FCFA
                                        @else
                                            {{ number_format($mouvement->montant ?? 0, 0, ',', ' ') }} FCFA
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(in_array($mouvement->type_mouvement, ['emprunt', 'pret']))
                                            {{ number_format($mouvement->montant - ($mouvement->montant_restant ?? 0), 0, ',', ' ') }} FCFA
                                        @elseif($mouvement->type_mouvement === 'remboursement')
                                            {{ number_format($mouvement->montant ?? 0, 0, ',', ' ') }} FCFA
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(in_array($mouvement->type_mouvement, ['emprunt', 'pret']))
                                            {{ number_format($mouvement->montant_restant ?? 0, 0, ',', ' ') }} FCFA
                                        @elseif($mouvement->type_mouvement === 'remboursement')
                                            {{ number_format($mouvement->mouvementReference->montant_restant ?? 0, 0, ',', ' ') }} FCFA
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $mouvement->motif ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $mouvement->user->nom_util ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="showMouvementDetails({{ $mouvement->id_mouvement }})" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Détails
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal pour les détails -->
                <div id="mouvementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Détails du mouvement</h3>
                            <div class="mt-2 px-7 py-3">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Solde avant</label>
                                        <p id="modalSoldeAvant" class="mt-1 text-lg font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Solde après</label>
                                        <p id="modalSoldeApres" class="mt-1 text-lg font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Différence</label>
                                        <p id="modalDifference" class="mt-1 text-lg font-semibold"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="closeModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>

                @else
                <div class="mt-8 text-center text-gray-500">
                    Aucun mouvement enregistré pour cette caisse.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showMouvementDetails(mouvementId) {
    fetch(`/mouvements/${mouvementId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalSoldeAvant').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.solde_avant) + ' FCFA';
            document.getElementById('modalSoldeApres').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.solde_apres) + ' FCFA';
            
            const difference = data.solde_apres - data.solde_avant;
            const differenceElement = document.getElementById('modalDifference');
            differenceElement.textContent = 
                new Intl.NumberFormat('fr-FR').format(Math.abs(difference)) + ' FCFA';
            differenceElement.className = 
                `mt-1 text-lg font-semibold ${difference >= 0 ? 'text-green-600' : 'text-red-600'}`;

            document.getElementById('mouvementModal').classList.remove('hidden');
        });
}

document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('mouvementModal').classList.add('hidden');
});

document.getElementById('mouvementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush

@endsection