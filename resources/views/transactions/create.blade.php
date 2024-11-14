@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Nouvelle Transaction</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- ID Utilisateur caché -->
                <input type="hidden" name="user_id" value="{{ auth()->user()->id_util }}">

                <!-- Type de Transaction -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Type de Transaction</label>
                    <select name="type_transaction_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                            required>
                        <option value="">Sélectionnez un type</option>
                        @foreach($typeTransactions as $type)
                            <option value="{{ $type->id_type_transa }}" 
                                    {{ old('type_transaction_id') == $type->id_type_transa ? 'selected' : '' }}>
                                {{ $type->nom_type_transa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Produit -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Produit</label>
                    <select name="produit_id" 
                            id="produit_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                            required>
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id_prod }}" 
                                    data-balance="{{ $produit->balance }}"
                                    {{ old('produit_id') == $produit->id_prod ? 'selected' : '' }}>
                                {{ $produit->nom_prod }} (Solde: {{ number_format($produit->balance, 0, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Montant -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Montant de la transaction</label>
                    <input type="number" 
                           name="montant_trans" 
                           id="montant_trans"
                           value="{{ old('montant_trans') }}"
                           step="1" 
                           min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           required>
                </div>

                <!-- Commission -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Commission</label>
                    <input type="text" 
                           id="commission" 
                           readonly
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                </div>

                <!-- Numéro Bénéficiaire -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Numéro Bénéficiaire</label>
                    <input type="text" 
                           name="num_beneficiaire"
                           value="{{ old('num_beneficiaire') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           required>
                </div>

                <!-- Motifs -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Motif de la Transaction</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="motif[]" 
                                   value="transfert"
                                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ in_array('transfert', old('motif', [])) ? 'checked' : '' }}
                                   onclick="toggleOtherMotifs(this)">
                            <span class="ml-2">Transfert</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="motif[]" 
                                   value="paiement_ceet"
                                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ in_array('paiement_ceet', old('motif', [])) ? 'checked' : '' }}
                                   onclick="toggleOtherMotifs(this)">
                            <span class="ml-2">Paiement CEET</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="motif[]" 
                                   value="paiement_canal"
                                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ in_array('paiement_canal', old('motif', [])) ? 'checked' : '' }}
                                   onclick="toggleOtherMotifs(this)">
                            <span class="ml-2">Paiement Canal+</span>
                        </label>
                    </div>
                </div>

                <!-- Frais de service -->
                <div class="mb-4" id="fraisServiceContainer" style="display: block;">
                    <label for="frais_service" class="block text-sm font-medium text-gray-700">Frais de service</label>
                    <input type="number" name="frais_service" id="frais_service" value="{{ old('frais_service') }}" 
                        step="0.01" min="0" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                        disabled>
                </div>

                <!-- Message d'information (si applicable) -->
                <div id="fraisServiceMessage" style="display:none;" class="text-sm text-red-600 mt-2">
                    Les frais de service ne sont pas applicables pour un transfert.
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('transactions.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fraisServiceContainer = document.getElementById('fraisServiceContainer');
    const fraisServiceInput = document.getElementById('frais_service');
    const motifCheckboxes = document.querySelectorAll('.motif-checkbox');
    const fraisServiceMessage = document.getElementById('fraisServiceMessage'); // Message informatif

    // Fonction pour gérer la sélection des motifs
    function toggleOtherMotifs(selectedCheckbox) {
        // Si un motif est sélectionné, désactiver les autres
        motifCheckboxes.forEach(checkbox => {
            if (checkbox !== selectedCheckbox) {
                checkbox.disabled = selectedCheckbox.checked;
            }
        });
        
        // Mettre à jour l'affichage des frais de service en fonction du motif sélectionné
        toggleFraisService();
    }

    // Fonction pour activer/désactiver le champ des frais de service
    function toggleFraisService() {
        const transfertChecked = document.querySelector('input[value="transfert"]').checked;
        const ceetChecked = document.querySelector('input[value="paiement_ceet"]').checked;
        const canalChecked = document.querySelector('input[value="paiement_canal"]').checked;

        // Si 'Transfert' est coché, cacher et désactiver les frais de service
        if (transfertChecked) {
            fraisServiceInput.disabled = true;  // Désactiver le champ
            fraisServiceContainer.style.display = 'none';  // Cacher le champ
            fraisServiceMessage.style.display = 'block';  // Afficher message
        } else if (ceetChecked || canalChecked) {
            fraisServiceInput.disabled = false;  // Activer le champ
            fraisServiceContainer.style.display = 'block';  // Afficher le champ
            fraisServiceMessage.style.display = 'none'; // Cacher message
        } else {
            fraisServiceInput.disabled = true;  // Désactiver le champ
            fraisServiceContainer.style.display = 'none';  // Cacher le champ
            fraisServiceMessage.style.display = 'none'; // Cacher message
        }
    }

    // Ajouter les écouteurs d'événements sur les cases à cocher
    motifCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleOtherMotifs(this);
        });
    });

    // Vérifier l'état initial au chargement de la page
    toggleFraisService();
});
</script>
@endpush
@endsection
