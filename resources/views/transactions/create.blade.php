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

                <!-- Caisse -->
                <!-- Caisse -->
<div class="mb-4">
    <label for="id_caisse" class="block text-sm font-medium text-gray-700">Sélectionner une Caisse</label>
    <select id="id_caisse" 
            name="id_caisse" 
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            required>
        <option value="">-- Sélectionnez une caisse --</option>
        @foreach($caisses as $caisse)
            <option value="{{ $caisse->id_caisse }}" {{ old('id_caisse') == $caisse->id_caisse ? 'selected' : '' }}>
                {{ $caisse->nom_caisse }} - {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA
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
                                    {{ old('id_prod') == $produit->id_prod ? 'selected' : '' }}>
                                {{ $produit->nom_prod }} (Solde: {{ number_format($produit->balance, 0, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Montant -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Montant de la transaction</label>
                    <input type="number" 
                           name="montant" 
                           id="montant" 
                           value="{{ old('montant') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <!-- Commission (en lecture seule) -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Commission</label>
                    <input type="text" 
                           id="commission_display" 
                           readonly
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                           placeholder="La commission sera calculée automatiquement">
                    <input type="hidden" 
                           name="commission_appliquee" 
                           id="commission_appliquee"
                           required>
                    <div id="commission_error" class="text-red-500 text-sm hidden">
                        La commission est requise. Veuillez vérifier le montant et le type de transaction.
                    </div>
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
                <!-- Motifs -->
<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">Motif de la Transaction</label>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <label class="inline-flex items-center">
            <input type="radio" 
                   name="motif" 
                   value="transfert"
                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   {{ old('motif') == 'transfert' ? 'checked' : '' }}>
            <span class="ml-2">Transfert</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" 
                   name="motif" 
                   value="paiement_ceet"
                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   {{ old('motif') == 'paiement_ceet' ? 'checked' : '' }}>
            <span class="ml-2">Paiement CEET</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" 
                   name="motif" 
                   value="paiement_canal"
                   class="motif-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   {{ old('motif') == 'paiement_canal' ? 'checked' : '' }}>
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

    const montantInput = document.getElementById('montant');
    const typeTransactionSelect = document.querySelector('select[name="type_transaction_id"]');
    const produitSelect = document.querySelector('select[name="produit_id"]');
    const commissionDisplay = document.getElementById('commission_display');
    const commissionInput = document.getElementById('commission_appliquee');

    async function updateCommission() {
        const montant = montantInput.value;
        const typeTransactionId = typeTransactionSelect.value;
        const produitId = produitSelect.value;
        const commissionError = document.getElementById('commission_error');

        console.log('Tentative de calcul avec:', { montant, typeTransactionId, produitId });

        // Réinitialiser l'affichage
        commissionDisplay.value = '';
        commissionInput.value = '';
        if (commissionError) {
            commissionError.classList.add('hidden');
        }

        if (montant && typeTransactionId && produitId) {
            try {
                // Construire l'URL avec les paramètres de requête
                const url = `${window.location.origin}/api/commission/calculate?montant_trans=${montant}&type_transaction_id=${typeTransactionId}&produit_id=${produitId}`;

                console.log('URL de la requête:', url);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Réponse du serveur:', errorText);
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Données reçues:', data);

                if (data.success) {
                    const commission = parseFloat(data.commission);
                    if (isNaN(commission)) {
                        throw new Error('La commission reçue n\'est pas un nombre valide');
                    }
                    commissionDisplay.value = new Intl.NumberFormat('fr-FR').format(commission) + ' FCFA';
                    commissionInput.value = commission;
                    if (commissionError) {
                        commissionError.classList.add('hidden');
                    }
                } else {
                    throw new Error(data.message || 'Aucune commission trouvée');
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                commissionDisplay.value = 'Erreur de calcul';
                commissionInput.value = '';
                if (commissionError) {
                    commissionError.textContent = error.message;
                    commissionError.classList.remove('hidden');
                }
            }
        } else {
            commissionDisplay.value = 'Veuillez remplir tous les champs';
            if (commissionError) {
                commissionError.textContent = 'Tous les champs sont requis';
                commissionError.classList.remove('hidden');
            }
        }
    }

    // Ajouter les écouteurs d'événements pour le calcul de la commission
    montantInput.addEventListener('input', updateCommission);
    typeTransactionSelect.addEventListener('change', updateCommission);
    produitSelect.addEventListener('change', updateCommission);

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const commission = document.getElementById('commission_appliquee').value;
        console.log('Valeur de la commission lors de la soumission:', commission);
        
        if (!commission || isNaN(parseFloat(commission))) {
            e.preventDefault();
            alert('La commission n\'est pas correctement calculée. Veuillez réessayer.');
            return false;
        }
    });
});
</script>
@endpush
@endsection