@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Détails du Mouvement</h1>
                        <p class="text-gray-300">Référence: {{ $mouvement->reference }}</p>
                    </div>
                    <a href="{{ route('produits.index') }}"
                        class="bg-blue-600/20 hover:bg-blue-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-blue-500/30">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <!-- Informations du Produit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Informations du Produit</h2>
                    <div class="space-y-3">
                        <p class="text-gray-300">Nom: <span class="text-white">{{ $mouvement->produit->nom_prod }}</span>
                        </p>
                        <p class="text-gray-300">Balance avant: <span
                                class="text-white">{{ number_format($mouvement->produit->balance - $mouvement->commission_produit, 0, ',', ' ') }}
                                FCFA</span></p>
                        <p class="text-gray-300">Balance après: <span
                                class="text-white">{{ number_format($mouvement->produit->balance, 0, ',', ' ') }}
                                FCFA</span></p>
                        <p class="text-gray-300">Date du mouvement: <span
                                class="text-white">{{ $mouvement->created_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>

                <!-- Résumé des Commissions -->
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Résumé des Commissions</h2>
                    <div class="space-y-3">
                        <p class="text-gray-300">Commission Totale: <span
                                class="text-white font-bold">{{ number_format($mouvement->commission_produit, 0, ',', ' ') }}
                                FCFA</span></p>
                        <p class="text-gray-300">Montant HT: <span
                                class="text-white">{{ number_format($mouvement->montant_ht, 0, ',', ' ') }} FCFA</span></p>
                        <p class="text-gray-300">Retenue: <span
                                class="text-white">{{ number_format($mouvement->retenue, 0, ',', ' ') }} FCFA</span></p>
                        <p class="text-gray-300">Montant Net: <span
                                class="text-white">{{ number_format($mouvement->montant_net, 0, ',', ' ') }} FCFA</span></p>
                    </div>
                </div>
            </div>

            <!-- Détails des Transactions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dépôts -->
                <div class="bg-blue-500/10 backdrop-blur-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Dépôts</h2>
                    <div class="space-y-3">
                        <p class="text-gray-300">Volume: <span class="text-white">{{ $mouvement->volume_depot }}</span></p>
                        <p class="text-gray-300">Valeur: <span
                                class="text-white">{{ number_format($mouvement->valeur_depot, 0, ',', ' ') }} FCFA</span>
                        </p>
                        <p class="text-gray-300">Commission: <span
                                class="text-white">{{ number_format($mouvement->commission_depot, 0, ',', ' ') }}
                                FCFA</span></p>
                    </div>
                </div>

                <!-- Retraits -->
                <div class="bg-green-500/10 backdrop-blur-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Retraits</h2>
                    <div class="space-y-3">
                        <p class="text-gray-300">Volume: <span class="text-white">{{ $mouvement->volume_retrait }}</span>
                        </p>
                        <p class="text-gray-300">Valeur: <span
                                class="text-white">{{ number_format($mouvement->valeur_retrait, 0, ',', ' ') }} FCFA</span>
                        </p>
                        <p class="text-gray-300">Commission: <span
                                class="text-white">{{ number_format($mouvement->commission_retrait, 0, ',', ' ') }}
                                FCFA</span></p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mt-6">
                <h2 class="text-xl font-semibold text-white mb-4">Description</h2>
                <p class="text-gray-300">{{ $mouvement->description }}</p>
            </div>
        </div>
    </div>
@endsection
