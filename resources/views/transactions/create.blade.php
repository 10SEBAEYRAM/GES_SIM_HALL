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
                                   {{ in_array('transfert', old('motif', [])) ? 'checked' : '' }}>
                            <span class="ml-2">Transfert</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="motif[]" 
                                   value="paiement_ceet"
                                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ in_array('paiement_ceet', old('motif', [])) ? 'checked' : '' }}>
                            <span class="ml-2">Paiement CEET</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="motif[]" 
                                   value="paiement_canal"
                                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ in_array('paiement_canal', old('motif', [])) ? 'checked' : '' }}>
                            <span class="ml-2">Paiement Canal+</span>
                        </label>
                    </div>
                </div>

                <!-- Frais de service -->
                <div class="space-y-2" id="fraisServiceContainer">
                    <label for="frais_service" 
                           id="fraisServiceLabel"
                           class="block text-sm font-medium text-gray-700">
                        Frais de service
                    </label>
                    <input type="number" 
                           name="frais_service" 
                           id="frais_service" 
                           value="{{ old('frais_service') }}" 
                           step="0.01" 
                           min="0" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <div id="fraisServiceMessage" class="text-sm text-red-600 mt-2" style="display:none;">
                        Les frais de service ne sont pas applicables pour un transfert.
                    </div>
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
    const fraisServiceLabel = document.getElementById('fraisServiceLabel');
    const motifCheckboxes = document.querySelectorAll('.motif-checkbox');
    const fraisServiceMessage = document.getElementById('fraisServiceMessage');

    function handleMotifChange(selectedCheckbox) {
        // Désactiver les autres checkboxes
        motifCheckboxes.forEach(checkbox => {
            if (checkbox !== selectedCheckbox && selectedCheckbox.checked) {
                checkbox.checked = false;
                checkbox.disabled = true;
            } else {
                checkbox.disabled = false;
            }
        });

        // Mettre à jour l'état des frais de service
        updateFraisServiceState();
    }

    function updateFraisServiceState() {
        const transfertChecked = document.querySelector('input[value="transfert"]').checked;
        const ceetChecked = document.querySelector('input[value="paiement_ceet"]').checked;
        const canalChecked = document.querySelector('input[value="paiement_canal"]').checked;

        if (ceetChecked || canalChecked) {
            // Activer pour CEET ou Canal+
            fraisServiceContainer.style.display = 'block';
            fraisServiceInput.disabled = false;
            fraisServiceInput.classList.remove('bg-gray-100');
            fraisServiceLabel.classList.remove('text-gray-400');
            fraisServiceMessage.style.display = 'none';
        } else if (transfertChecked) {
            // Désactiver pour transfert
            fraisServiceContainer.style.display = 'block';
            fraisServiceInput.disabled = true;
            fraisServiceInput.classList.add('bg-gray-100');
            fraisServiceLabel.classList.add('text-gray-400');
            fraisServiceMessage.style.display = 'block';
        } else {
            // État par défaut
            fraisServiceContainer.style.display = 'block';
            fraisServiceInput.disabled = true;
            fraisServiceInput.classList.add('bg-gray-100');
            fraisServiceLabel.classList.add('text-gray-400');
            fraisServiceMessage.style.display = 'none';
        }
    }

    // Ajouter les écouteurs d'événements
    motifCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleMotifChange(this);
        });
    });

    // Initialiser l'état
    updateFraisServiceState();
});
</script>
@endpush
@endsection