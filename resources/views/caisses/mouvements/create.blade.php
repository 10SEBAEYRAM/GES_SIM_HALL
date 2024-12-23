@extends('layouts.app')

@section('title', 'Mouvement de Caisse')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Nouveau Mouvement de Caisse</h2>

                <!-- Affichage des erreurs de validation -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erreurs de validation:</h3>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('caisses.mouvements.store') }}" method="POST"> @csrf
                    <!-- Type de mouvement -->
                    <div class="mb-4">
                        <label for="type_mouvement" class="block text-sm font-medium text-gray-700">Type de
                            Mouvement</label>
                        <select id="type_mouvement" name="type_mouvement" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('type_mouvement') border-red-500 @enderror">
                            <option value="">Sélectionnez un type</option>
                            <option value="emprunt" {{ old('type_mouvement') == 'emprunt' ? 'selected' : '' }}>Emprunt
                            </option>
                            <option value="remboursement" {{ old('type_mouvement') == 'remboursement' ? 'selected' : '' }}>
                                Remboursement</option>
                            <option value="retrait" {{ old('type_mouvement') == 'retrait' ? 'selected' : '' }}>Retrait
                            </option>
                            <option value="pret" {{ old('type_mouvement') == 'pret' ? 'selected' : '' }}>Prêt</option>
                        </select>
                        @error('type_mouvement')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type d'opération à rembourser -->
                    <div class="mb-4" id="type-remboursement" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700">Type d'opération à rembourser</label>
                        <select id="type-operation" name="type_operation"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('type_operation') border-red-500 @enderror">
                            <option value="">Sélectionnez le type d'opération</option>
                            <option value="emprunt">Emprunt à rembourser</option>
                            <option value="pret">Prêt à rembourser</option>
                        </select>
                        @error('type_operation')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Référence emprunt -->
                    <div class="mb-4" id="reference-emprunt" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700">Emprunt à rembourser</label>
                        <select id="motif_reference_emprunt" name="motif_reference"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('motif_reference') border-red-500 @enderror">
                            <option value="">Sélectionnez l'emprunt à rembourser</option>
                            <option value="test">Test</option>
                        </select>
                        @error('motif_reference')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Référence prêt -->
                    <div class="mb-4" id="reference-pret" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700">Prêt à rembourser</label>
                        <select name="motif_reference" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                            <option value="">Sélectionnez le prêt à rembourser</option>
                        </select>
                    </div>

                    <!-- Caisse -->
                    <div class="mb-4">
                        <label for="id_caisse" class="block text-sm font-medium text-gray-700">Caisse</label>
                        <select id="id_caisse" name="id_caisse" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('id_caisse') border-red-500 @enderror">
                            <option value="">Sélectionnez une caisse</option>
                            @foreach ($caisses as $caisse)
                                <option value="{{ $caisse->id_caisse }}"
                                    {{ old('id_caisse') == $caisse->id_caisse ? 'selected' : '' }}
                                    data-solde="{{ $caisse->balance_caisse }}"
                                    data-emprunt="{{ $caisse->emprunt_sim_hall }}">
                                    {{ $caisse->nom_caisse }} (Solde:
                                    {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA)
                                </option>
                            @endforeach
                        </select>
                        @error('id_caisse')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div class="mb-4">
                        <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" id="montant" name="montant" value="{{ old('montant') }}" required
                                min="0" step="1"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('montant') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                        @error('montant')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p id="solde-info" class="mt-2 text-sm text-gray-500"></p>
                    </div>

                    <!-- Motif -->
                    <div class="mb-4">
                        <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                        <textarea id="motif" name="motif" rows="3" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('motif') border-red-500 @enderror">{{ old('motif') }}</textarea>
                        @error('motif')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="{{ route('caisses.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-300">
                            Annuler
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-300">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeMouvementSelect = document.getElementById('type_mouvement');
            const typeOperationDiv = document.getElementById('type-remboursement');
            const referenceEmpruntDiv = document.getElementById('reference-emprunt');
            const referencePretDiv = document.getElementById('reference-pret');
            const typeOperationSelect = document.getElementById('type-operation');
            const motifReferenceEmprunt = document.getElementById('motif_reference_emprunt');
            const motifReferencePret = document.getElementById('motif_reference_pret');

            // Afficher/masquer les champs en fonction du type de mouvement
            typeMouvementSelect.addEventListener('change', function() {
                if (this.value === 'remboursement') {
                    typeOperationDiv.style.display = 'block';
                } else {
                    typeOperationDiv.style.display = 'none';
                    referenceEmpruntDiv.style.display = 'none';
                    referencePretDiv.style.display = 'none';
                }
            });

            // Gérer l'affichage des références en fonction du type d'opération
            typeOperationSelect.addEventListener('change', function() {
                const caisseId = document.getElementById('id_caisse').value;
                
                if (!caisseId) {
                    alert('Veuillez d\'abord sélectionner une caisse');
                    this.value = '';
                    return;
                }

                if (this.value === 'emprunt') {
                    referenceEmpruntDiv.style.display = 'block';
                    referencePretDiv.style.display = 'none';
                    chargerOperations(caisseId, 'emprunt', motifReferenceEmprunt);
                } else if (this.value === 'pret') {
                    referenceEmpruntDiv.style.display = 'none';
                    referencePretDiv.style.display = 'block';
                    chargerOperations(caisseId, 'pret', motifReferencePret);
                }
            });

            // Fonction pour charger les opérations
            function chargerOperations(caisseId, type, selectElement) {
                fetch(`/api/caisses/${caisseId}/operations-non-remboursees?type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        selectElement.innerHTML = '<option value="">Sélectionnez l\'opération à rembourser</option>';
                        data.forEach(operation => {
                            selectElement.innerHTML += `
                                <option value="${operation.id_mouvement}">
                                    ${operation.motif} - Restant: ${operation.montant_restant} FCFA (${operation.date})
                                </option>`;
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
            }

            // Validation du formulaire avant soumission
            document.querySelector('form').addEventListener('submit', function(e) {
                if (typeMouvementSelect.value === 'remboursement') {
                    const selectedType = typeOperationSelect.value;
                    const visibleSelect = document.querySelector(
                        'select[name="motif_reference"]:not([style*="display: none"])');
                    const motifReference = visibleSelect ? visibleSelect.value : '';

                    if (!selectedType || !motifReference) {
                        e.preventDefault();
                        alert('Veuillez sélectionner une opération à rembourser');
                        return false;
                    }
                }
            });

            // Validation du montant de remboursement
            const montantInput = document.querySelector('input[name="montant"]');
            const referenceSelects = document.querySelectorAll('select[name="motif_reference"]');

            referenceSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.montantRestant) {
                        montantInput.max = selectedOption.dataset.montantRestant;
                        montantInput.setAttribute('data-max-remboursement', selectedOption.dataset
                            .montantRestant);
                    }
                });
            });

            montantInput.addEventListener('input', function() {
                const maxRemboursement = this.getAttribute('data-max-remboursement');
                if (maxRemboursement && parseFloat(this.value) > parseFloat(maxRemboursement)) {
                    this.setCustomValidity(`Le montant ne peut pas dépasser ${maxRemboursement} FCFA`);
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
@endpush
