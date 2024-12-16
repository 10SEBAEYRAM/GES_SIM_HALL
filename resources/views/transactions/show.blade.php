@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-8 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- En-tête -->
                <div class="p-6 sm:p-8 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Détails de la Transaction</h2>
                        <a href="{{ route('transactions.index') }}"
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
                        <!-- Date et Opérateur -->
                        <div class="bg-yellow-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Date/Heure</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Opérateur</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">{{ $transaction->user->nom_util }}
                                    {{ $transaction->user->prenom_util }}</p>
                            </div>
                        </div>

                        <!-- Informations de transaction -->
                        <div class="bg-blue-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Type de Transaction</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ $transaction->typeTransaction->nom_type_transa }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Produit</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ $transaction->produit->nom_prod ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="bg-green-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Montant</h3>
                                <p class="mt-2 text-lg font-bold text-gray-900">
                                    {{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Commission</h3>
                                <p class="mt-2 text-base font-semibold text-indigo-600">
                                    {{ number_format($transaction->commission_appliquee, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>

                        <!-- Statut et Motif -->
                        <!-- Statut et Motif -->
                        <div class="bg-purple-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Statut</h3>
                                <span
                                    class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if ($transaction->statut === 'COMPLETE') bg-green-200 text-green-800
                                        @elseif($transaction->statut === 'ANNULE') bg-red-200 text-red-800
                                        @else bg-yellow-200 text-yellow-800 @endif">
                                    {{ $transaction->statut }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Motif</h3>
                                <p class="mt-2 text-base text-gray-900">{{ $transaction->motif ?: 'Aucun motif spécifié' }}
                                </p>
                            </div>
                            <!-- Modification ici : Afficher les frais de service s'ils existent -->
                            @if ($transaction->frais_service > 0)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Frais Service</h3>
                                    <p class="mt-2 text-base font-semibold text-gray-900">
                                        {{ number_format($transaction->frais_service, 0, ',', ' ') }} FCFA</p>
                                </div>
                            @endif
                        </div>


                        <!-- Solde Produit -->
                        <div class="bg-red-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Solde Avant</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ number_format($transaction->solde_avant, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Solde Après</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ number_format($transaction->solde_apres, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>

                        <!-- Solde Caisse -->
                        <div class="bg-indigo-100 rounded-xl p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Solde Caisse Avant</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ number_format($transaction->solde_caisse_avant, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Solde Caisse Après</h3>
                                <p class="mt-2 text-base font-semibold text-gray-900">
                                    {{ number_format($transaction->solde_caisse_apres, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>

                        <!-- Bénéficiaire -->
                        <div class="bg-teal-100 rounded-xl p-6 space-y-4">
                            <h3 class="text-sm font-medium text-gray-500">Bénéficiaire</h3>
                            <p class="mt-2 text-base font-semibold text-gray-900">{{ $transaction->num_beneficiaire }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
