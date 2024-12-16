<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr>
            <th class="border border-gray-300 px-4 py-2">#</th>
            <th class="border border-gray-300 px-4 py-2">Type de transaction</th>
            <th class="border border-gray-300 px-4 py-2">Montant</th>
            <th class="border border-gray-300 px-4 py-2">Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $transaction)
     
        <tr>
            <td class="border border-gray-300 px-4 py-2">{{ $loop->iteration }}</td>
            <td class="border border-gray-300 px-4 py-2">
            
            @php
            $type_transaction = App\Models\TypeTransaction::find($transaction->type_transaction_id);
            @endphp
                {{ $type_transaction->nom_type_transa }}
            </td>
            <td class="border border-gray-300 px-4 py-2 text-right">
                {{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA
            </td>
            <td class="border border-gray-300 px-4 py-2 text-center">
                {{ $transaction->created_at->format('d/m/Y') }}
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">Aucune transaction trouvée</td>
        </tr>
        @endforelse
    </tbody>
</table>