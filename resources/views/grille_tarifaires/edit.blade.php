@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8 animate__animated animate__fadeInDown">
                <div class="flex justify-between items-center">
                    <a href="{{ route('grille_tarifaires.create') }}"
   class="bg-blue-600/20 hover:bg-blue-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-blue-500/30"
   onclick="return handleUnauthorized('{{ auth()->user()->can('create-grille_tarifaires') }}', 'créer une nouvelle grille')">
    <i class="fas fa-plus"></i>
    <span>Nouvelle Grille</span>
</a>
                    <a href="{{ route('grille_tarifaires.index') }}"
                        class="bg-gray-600/20 hover:bg-gray-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-gray-500/30">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <!-- Messages de feedback -->
            @if ($errors->any())
                <div
                    class="bg-red-500/20 backdrop-blur-lg border-l-4 border-red-500 text-white px-6 py-4 rounded-lg mb-6 animate__animated animate__fadeInDown">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Formulaire -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 animate__animated animate__fadeInUp">
               <form action="{{ route('grille-tarifaires.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <!-- Type de Transaction -->
    <div>
        <label class="block text-sm font-medium text-gray-200 mb-2">Type de Transaction</label>
        <select name="type_transaction_id" required
            class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Sélectionnez un type</option>
            @foreach($typeTransactions as $type)
                <option value="{{ $type->id_type_transa }}">
                    {{ $type->nom_type_transa }}
                </option>
            @endforeach
        </select>
        @error('type_transaction_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Produit -->
    <div>
        <label class="block text-sm font-medium text-gray-200 mb-2">Produit</label>
        <select name="produit_id" required
            class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Sélectionnez un produit</option>
            @foreach($produits as $produit)
                <option value="{{ $produit->id_prod }}" {{ old('produit_id') == $produit->id_prod ? 'selected' : '' }}>
                    {{ $produit->nom_prod }}
                </option>
            @endforeach
        </select>
        @error('produit_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Montants et Commission -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-200 mb-2">Montant Minimum</label>
            <input type="number" name="montant_min" value="{{ old('montant_min') }}" required
                class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('montant_min')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-200 mb-2">Montant Maximum</label>
            <input type="number" name="montant_max" value="{{ old('montant_max') }}" required
                class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('montant_max')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-200 mb-2">Commission</label>
            <input type="number" step="0.01" name="commission_grille_tarifaire" value="{{ old('commission_grille_tarifaire') }}" required
                class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('commission_grille_tarifaire')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('grille-tarifaires.index') }}" 
           class="bg-gray-600/50 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
            Annuler
        </a>
        <button type="submit" 
                class="bg-blue-600/50 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
            Enregistrer
        </button>
    </div>
</form>
            </div>
        </div>
    </div>
@endsection