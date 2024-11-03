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

            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Type de Transaction</label>
                    <select name="type_transaction_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Sélectionnez un type</option>
                        @foreach($typeTransactions as $type)
                            <option value="{{ $type->id_type_transa }}" {{ old('type_transaction_id') == $type->id_type_transa ? 'selected' : '' }}>
                                {{ $type->nom_type_transa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Produit</label>
                    <select name="produit_id" id="produit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id_prod }}" 
                                    data-balance="{{ $produit->balance }}"
                                    {{ old('produit_id') == $produit->id_prod ? 'selected' : '' }}>
                                {{ $produit->nom_prod }} (Balance: {{ number_format($produit->balance, 0, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Montant</label>
                    <input type="number" name="montant_trans" id="montant_trans" value="{{ old('montant_trans') }}" 
                           step="1" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Commission (calculée automatiquement)</label>
                    <input type="text" id="commission" readonly
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Numéro Bénéficiaire</label>
                    <input type="text" name="num_beneficiaire" value="{{ old('num_beneficiaire') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('transactions.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const produitSelect = document.getElementById('produit_id');
    const montantInput = document.getElementById('montant_trans');
    const commissionDisplay = document.getElementById('commission');

    function updateCommission() {
        const produitId = produitSelect.value;
        const montant = montantInput.value;

        if (produitId && montant) {
            fetch(`{{ route('transactions.get-commission') }}?produit_id=${produitId}&montant=${montant}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commissionDisplay.value = new Intl.NumberFormat('fr-FR', {
                        style: 'currency',
                        currency: 'XOF'
                    }).format(data.commission);
                } else {
                    commissionDisplay.value = 'Erreur de calcul';
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                commissionDisplay.value = 'Erreur de calcul';
            });
        } else {
            commissionDisplay.value = '';
        }
    }

    produitSelect.addEventListener('change', updateCommission);
    montantInput.addEventListener('input', updateCommission);
});
</script>
@endpush
@endsection 