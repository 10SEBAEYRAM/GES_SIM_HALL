@extends('layouts.app')

@section('title', 'Mouvement de Caisse')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Nouveau Mouvement de Caisse</h2>

            <form action="{{ route('caisses.caisse_transactions.store') }}" method="POST">
                @csrf

                <!-- Type de mouvement -->
                <div class="mb-4">
                    <label for="type_mouvement" class="block text-sm font-medium text-gray-700">Type de Mouvement</label>
                    <select id="type_mouvement" name="type_mouvement" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="emprunt">Emprunt</option>
                        <option value="remboursement">Remboursement</option>
                        <option value="retrait">Retrait</option>
                    </select>
                </div>

                <!-- Caisse -->
                <div class="mb-4">
                    <label for="id_caisse" class="block text-sm font-medium text-gray-700">Caisse</label>
                    <select id="id_caisse" name="id_caisse" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        @foreach($caisses as $caisse)
                        <option value="{{ $caisse->id_caisse }}">
                            {{ $caisse->nom_caisse }} (Solde: {{ number_format($caisse->balance_caisse, 0, ',', ' ') }} FCFA)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Montant -->
                <div class="mb-4">
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                    <input type="number" id="montant" name="montant" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>

                <!-- Motif -->
                <div class="mb-4">
                    <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                    <textarea id="motif" name="motif" rows="3" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('caisses.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                        Annuler
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
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
        const typeMouvement = document.getElementById('type_mouvement');
        const montantInput = document.getElementById('montant');
        const caisseSelect = document.getElementById('id_caisse');

        // Validation du montant en fonction du type de mouvement et du solde de la caisse
        function validateMontant() {
            const selectedCaisse = caisseSelect.options[caisseSelect.selectedIndex];
            const caisseSolde = parseFloat(selectedCaisse.dataset.solde);
            const montant = parseFloat(montantInput.value);
            const type = typeMouvement.value;

            if (type === 'retrait' || type === 'emprunt') {
                if (montant > caisseSolde) {
                    alert('Le montant ne peut pas être supérieur au solde de la caisse');
                    montantInput.value = '';
                }
            }
        }

        montantInput.addEventListener('change', validateMontant);
        typeMouvement.addEventListener('change', validateMontant);
    });
</script>
@endpush