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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opérateur</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mouvements as $mouvement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $mouvement->created_at ? $mouvement->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($mouvement->type_mouvement ?? 'N/A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($mouvement->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-6 py-4">{{ $mouvement->motif ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $mouvement->user->nom_util ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
@endsection