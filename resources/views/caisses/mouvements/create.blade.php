@extends('layouts.app')

@section('title', 'Mouvement de Caisse')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Nouveau Mouvement de Caisse</h2>

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

                <form action="{{ route('caisses.mouvements.store') }}" method="POST" id="mouvementForm">
                    @csrf

                    <!-- Sélection de la Caisse -->
                    <div class="mb-4">
                        <label for="id_caisse" class="block text-sm font-medium text-gray-700">Caisse</label>
                        <select id="id_caisse" name="id_caisse" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('id_caisse') border-red-500 @enderror">
                            <option value="">Sélectionnez une caisse</option>
                            @foreach ($caisses as $caisse)
                                <option value="{{ $caisse->id_caisse }}"
                                    {{ old('id_caisse') == $caisse->id_caisse ? 'selected' : '' }}
                                    data-solde="{{ $caisse->balance_caisse }}">
                                    {{ $caisse->nom_caisse }} (Solde:
                                    {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA)
                                </option>
                            @endforeach
                        </select>
                        @error('id_caisse')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de Mouvement -->
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

                    <!-- Options de Remboursement -->
                    <div id="type-remboursement" style="display: none;">
                        <div class="mb-4">
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

                        <!-- Sélection de l'opération à rembourser -->
                        <div id="reference-emprunt" class="mb-4" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Emprunt à rembourser</label>
                            <select id="motif_reference_emprunt" name="motif_reference"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('motif_reference') border-red-500 @enderror">
                                <option value="">Sélectionnez l'emprunt à rembourser</option>
                            </select>
                            @error('motif_reference')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="reference-pret" class="mb-4" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Prêt à rembourser</label>
                            <select id="motif_reference_pret" name="motif_reference"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('motif_reference') border-red-500 @enderror">
                                <option value="">Sélectionnez le prêt à rembourser</option>
                            </select>
                            @error('motif_reference')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="motif_reference" id="hidden_motif_reference" value="">
                    </div>

                    <!-- Montant -->
                    <div class="mb-4">
                        <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" id="montant" name="montant" value="{{ old('montant') }}" required
                                min="0" step="1"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md @error('montant') border-red-500 @enderror">
                        </div>
                        <p id="solde-info" class="mt-1 text-sm"></p>
                        @error('montant')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('caisses.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Annuler
                        </a>
                        <button type="submit" id="submitButton"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
            const form = document.getElementById('mouvementForm');
            const typeMouvement = document.getElementById('type_mouvement');
            const typeRemboursement = document.getElementById('type-remboursement');
            const typeOperation = document.getElementById('type-operation');
            const referenceEmprunt = document.getElementById('reference-emprunt');
            const referencePret = document.getElementById('reference-pret');
            const motifReferenceEmprunt = document.getElementById('motif_reference_emprunt');
            const motifReferencePret = document.getElementById('motif_reference_pret');
            const montantInput = document.getElementById('montant');
            const hiddenMotifReference = document.getElementById('hidden_motif_reference');

            // Gestion du type de mouvement
            typeMouvement.addEventListener('change', function() {
                typeRemboursement.style.display = this.value === 'remboursement' ? 'block' : 'none';
                if (this.value !== 'remboursement') {
                    typeOperation.value = '';
                    referenceEmprunt.style.display = 'none';
                    referencePret.style.display = 'none';
                    montantInput.removeAttribute('max');
                    montantInput.removeAttribute('data-max-remboursement');
                    document.getElementById('solde-info').textContent = '';
                }
            });

            // Gestion du type d'opération
            typeOperation.addEventListener('change', function() {
                const caisseId = document.getElementById('id_caisse').value;
                if (!caisseId) {
                    alert('Veuillez d\'abord sélectionner une caisse');
                    this.value = '';
                    return;
                }

                referenceEmprunt.style.display = 'none';
                referencePret.style.display = 'none';

                if (this.value === 'emprunt') {
                    referenceEmprunt.style.display = 'block';
                    referencePret.style.display = 'none';
                    motifReferencePret.removeAttribute('name');
                    motifReferenceEmprunt.setAttribute('name', 'motif_reference');
                    chargerOperations(caisseId, 'emprunt', motifReferenceEmprunt);
                } else if (this.value === 'pret') {
                    referencePret.style.display = 'block';
                    referenceEmprunt.style.display = 'none';
                    motifReferenceEmprunt.removeAttribute('name');
                    motifReferencePret.setAttribute('name', 'motif_reference');
                    chargerOperations(caisseId, 'pret', motifReferencePret);
                }
            });

            // Chargement des opérations
            function chargerOperations(caisseId, type, selectElement) {
                selectElement.innerHTML = '<option value="">Chargement...</option>';

                fetch(`/api/caisses/${caisseId}/operations-non-remboursees?type=${type}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
                        return response.json();
                    })
                    .then(data => {
                        selectElement.innerHTML = '<option value="">Sélectionnez une opération</option>';
                        data.forEach(operation => {
                            const option = document.createElement('option');
                            option.value = operation.id_mouvement;
                            option.textContent =
                                `${operation.motif} - Restant: ${operation.montant_restant} FCFA (${operation.date})`;
                            option.dataset.montantRestant = operation.montant_restant.replace(/\s/g,
                                '');
                            selectElement.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        selectElement.innerHTML = '<option value="">Erreur de chargement</option>';
                    });
            }

            // Validation du montant
            [motifReferenceEmprunt, motifReferencePret].forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.montantRestant) {
                        const maxMontant = parseFloat(selectedOption.dataset.montantRestant);
                        montantInput.setAttribute('max', maxMontant);
                        montantInput.setAttribute('data-max-remboursement', maxMontant);
                        document.getElementById('solde-info').textContent =
                            `Montant maximum autorisé : ${maxMontant.toLocaleString('fr-FR')} FCFA`;
                        document.getElementById('solde-info').classList.remove('text-red-500');

                        // Mettre à jour le champ caché
                        hiddenMotifReference.value = this.value;
                    }
                });
            });

            montantInput.addEventListener('input', function() {
                const maxRemboursement = this.getAttribute('data-max-remboursement');
                if (maxRemboursement) {
                    const montantSaisi = parseFloat(this.value);
                    const maxMontant = parseFloat(maxRemboursement);

                    if (montantSaisi > maxMontant) {
                        this.setCustomValidity(
                            `Le montant ne peut pas dépasser ${maxMontant.toLocaleString('fr-FR')} FCFA`
                        );
                        document.getElementById('solde-info').classList.add('text-red-500');
                    } else {
                        this.setCustomValidity('');
                        document.getElementById('solde-info').classList.remove('text-red-500');
                    }
                } else {
                    this.setCustomValidity('');
                }
            });

            // Fonction pour afficher les détails du mouvement
            window.showMouvementDetails = function(mouvementId) {
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
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des détails');
                    });
            }

            // Gestion de la fermeture du modal
            document.getElementById('closeModal')?.addEventListener('click', function() {
                document.getElementById('mouvementModal').classList.add('hidden');
            });

            document.getElementById('mouvementModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
            // Nouvelle gestion de la soumission du formulaire
            form.onsubmit = function(event) {
                event.preventDefault(); // Empêcher la soumission par défaut

                // Vérifier si c'est un remboursement
                if (typeMouvement.value === 'remboursement') {
                    const selectedType = typeOperation.value;
                    let motifReference = null;

                    if (referenceEmprunt.style.display === 'block') {
                        motifReference = motifReferenceEmprunt.value;
                    } else if (referencePret.style.display === 'block') {
                        motifReference = motifReferencePret.value;
                    }

                    if (!selectedType || !motifReference) {
                        alert('Veuillez sélectionner une opération à rembourser');
                        return false;
                    }

                    // Supprimer l'ancien champ caché s'il existe
                    const oldHiddenInput = form.querySelector('input[name="motif_reference"]');
                    if (oldHiddenInput) {
                        oldHiddenInput.remove();
                    }

                    // Ajouter le nouveau champ caché
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'motif_reference';
                    hiddenInput.value = motifReference;
                    form.appendChild(hiddenInput);
                }

                // Soumettre le formulaire
                console.log('Soumission du formulaire...'); // Pour le débogage
                form.submit();
                return false;
            };
        });
    </script>
@endpush
