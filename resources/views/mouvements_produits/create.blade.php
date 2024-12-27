@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-800 to-indigo-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Nouveau Mouvement</h1>
                        <p class="text-gray-300">Ajouter un mouvement au produit</p>
                    </div>
                    <a href="{{ route('produits.index') }}"
                        class="bg-blue-600/20 hover:bg-blue-600/30 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105 flex items-center space-x-2 border border-blue-500/30">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6">
                <form action="{{ route('mouvements-produits.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Sélection du Produit -->
                    <div>
                        <label for="produit_id" class="block text-sm font-medium text-gray-300">Produit</label>
                        <select name="produit_id" id="produit_id" required
                            class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionnez un produit</option>
                            @foreach ($produits as $produit)
                                <option value="{{ $produit->id_prod }}">{{ $produit->nom_prod }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Montant -->


                    <!-- Type de Mouvement -->
                    <div>
                        <label for="type_mouvement" class="block text-sm font-medium text-gray-300">Type de
                            Mouvement</label>
                        <select name="type_mouvement" id="type_mouvement" required
                            class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="CREDIT">Crédit</option>
                            <option value="DEBIT">Débit</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                        <textarea name="description" id="description" rows="3" required
                            class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>



                    <!-- Dans le formulaire, ajoutons les champs nécessaires -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Dépôts -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-white">Dépôts</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Volume</label>
                                <input type="number" name="volume_depot" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Valeur</label>
                                <input type="number" name="valeur_depot" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Commission</label>
                                <input type="number" name="commission_depot" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                        </div>

                        <!-- Retraits -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-white">Retraits</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Volume</label>
                                <input type="number" name="volume_retrait" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Valeur</label>
                                <input type="number" name="valeur_retrait" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Commission</label>
                                <input type="number" name="commission_retrait" required
                                    class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Montants totaux -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Montant HT</label>
                            <input type="number" name="montant_ht" required
                                class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Retenue</label>
                            <input type="number" name="retenue" required
                                class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Montant Net</label>
                            <input type="number" name="montant_net" required
                                class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white">
                        </div>
                    </div>
                    <!-- Commission totale du produit (calculée automatiquement) -->
                    <div>
                        <label for="commission_produit" class="block text-sm font-medium text-gray-300">Commission
                            Produit</label>
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <input type="number" name="commission_produit" id="commission_produit" step="0.01" readonly
                                class="mt-1 block w-full bg-white/10 border border-gray-600 rounded-lg shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-300 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-400">Commission totale qui sera ajoutée à la balance du produit</p>
                    </div>


                    <!-- Bouton de soumission -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 hover:scale-105">
                            Enregistrer le mouvement
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
            const commissionDepotInput = document.querySelector('[name="commission_depot"]');
            const commissionRetraitInput = document.querySelector('[name="commission_retrait"]');
            const retenueInput = document.querySelector('[name="retenue"]');
            const commissionProduitInput = document.querySelector('[name="commission_produit"]');

            function calculateTotalCommission() {
                const commissionDepot = parseFloat(commissionDepotInput.value) || 0;
                const commissionRetrait = parseFloat(commissionRetraitInput.value) || 0;
                const retenue = parseFloat(retenueInput.value) || 0;

                // Total = (Commission Dépôt + Commission Retrait) - Retenue
                const total = (commissionDepot + commissionRetrait) - retenue;
                commissionProduitInput.value = total.toFixed(2);
            }

            // Calculer à chaque changement des commissions ou de la retenue
            commissionDepotInput.addEventListener('input', calculateTotalCommission);
            commissionRetraitInput.addEventListener('input', calculateTotalCommission);
            retenueInput.addEventListener('input', calculateTotalCommission);
        });
    </script>
@endpush
