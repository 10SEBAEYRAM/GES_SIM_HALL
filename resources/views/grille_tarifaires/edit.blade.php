@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInDown">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Modifier la Grille Tarifaire</h1>
                    <p class="text-gray-300">Modification d'une grille tarifaire existante</p>
                </div>
                <a href="{{ route('grille-tarifaires.index') }}" 
                   class="bg-gray-600/20 hover:bg-gray-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-gray-500/30">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInUp">
            <form action="{{ route('grille-tarifaires.update', $grilleTarifaire->id_grille_tarifaire) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Type de Transaction -->
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Type de Transaction</label>
                    <select name="type_transaction_id" required
                        class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionnez un type</option>
                        @foreach($typeTransactions as $type)
                            <option value="{{ $type->id_type_transa }}" 
                                {{ $grilleTarifaire->type_transaction_id == $type->id_type_transa ? 'selected' : '' }}>
                                {{ $type->nom_type_transa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Produit -->
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Produit</label>
                    <select name="produit_id" required
                        class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id_prod }}" 
                                {{ $grilleTarifaire->produit_id == $produit->id_prod ? 'selected' : '' }}>
                                {{ $produit->nom_prod }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Montants et Commission -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">Montant Minimum</label>
                        <input type="number" name="montant_min" value="{{ $grilleTarifaire->montant_min }}" required
                            class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">Montant Maximum</label>
                        <input type="number" name="montant_max" value="{{ $grilleTarifaire->montant_max }}" required
                            class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">Commission</label>
                        <input type="number" step="0.01" name="commission_grille_tarifaire" 
                            value="{{ $grilleTarifaire->commission_grille_tarifaire }}" required
                            class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3">
                <a href="{{ route('grille-tarifaires.index') }}" 
                       class="bg-gray-600/50 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600/50 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 