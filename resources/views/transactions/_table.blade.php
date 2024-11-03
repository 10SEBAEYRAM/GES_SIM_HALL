<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date/Heure
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Produit
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Montant
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Commission
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Bénéficiaire
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Statut
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($transactions as $transaction)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $transaction->typeTransaction->nom_type_transa }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $transaction->produit->nom_prod }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ number_format($transaction->commission_appliquee, 0, ',', ' ') }} FCFA
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $transaction->num_beneficiaire }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($transaction->statut === 'COMPLETE') bg-green-100 text-green-800
                        @elseif($transaction->statut === 'ANNULE') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ $transaction->statut }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('transactions.show', $transaction->id_transaction) }}" 
                       class="text-blue-600 hover:text-blue-900">
                        Détails
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    Aucune transaction trouvée
                </td>
            </tr>
        @endforelse
    </tbody>
</table> 