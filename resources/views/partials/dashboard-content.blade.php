<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Total Utilisateurs -->
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">Total Utilisateurs</h2>
        <p class="text-2xl font-bold text-blue-500">{{ $totalUtilisateurs }}</p>
    </div>

    <!-- Total Produits -->
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">Total Produits</h2>
        <p class="text-2xl font-bold text-green-500">{{ $totalProduits }}</p>
    </div>

    <!-- Total Transactions -->
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">Total Transactions</h2>
        <p class="text-2xl font-bold text-purple-500">{{ $totalTransactions }}</p>
    </div>

    <!-- Balance Produits -->
    @foreach($produits as $produit)
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">{{ $produit->nom_prod }}</h2>
        <p class="text-2xl font-bold text-yellow-500">{{ number_format($produit->balance, 2) }} FCFA</p>
    </div>
    @endforeach
    <div class="bg-white shadow rounded-lg mt-6 p-4">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Balance des Produits</h2>
        <ul>
            @foreach ($produits as $produit)
            <li class="text-gray-600">
                {{ $produit->nom_prod }} : {{ number_format($produit->balance, 2) }} FCFA
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Balance Caisses -->
    @foreach($caisses as $caisse)
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">{{ $caisse->nom_caisse }}</h2>
        <p class="text-2xl font-bold text-red-500">{{ number_format($caisse->balance_caisse, 2) }} FCFA</p>
    </div>
    @endforeach
</div>
<div class="card mt-4">
    <div class="card-header">
        <h3>Transactions par Type</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type de Transaction</th>
                    <th>Montant Total</th>
                    <th>Nombre de Transactions</th>
                </tr>
            </thead>
            <tbody>


                @foreach ($type_transactions as $type_transaction)
                <tr>
                    <td>{{ $type_transaction->nom_type_transa }}</td>
                    <td>{{ number_format($type_transaction->total, 2) }} FCFA</td>
                </tr>
                @endforeach






                <tr>
                    <td colspan="3" class="text-center">Aucune transaction trouvée pour cette période.</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<div class="bg-white shadow rounded-lg mt-6 p-4">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Dernières Transactions</h2>
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2 text-left text-gray-600">ID</th>
                <th class="px-4 py-2 text-left text-gray-600">Utilisateur</th>
                <th class="px-4 py-2 text-left text-gray-600">Motif</th>
                <th class="px-4 py-2 text-left text-gray-600">Montant</th>
                <th class="px-4 py-2 text-left text-gray-600">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
            <tr>
                <td class="border px-4 py-2">{{ $transaction->id_transaction }}</td>
                <td class="border px-4 py-2">
                    {{ $transaction->user ? $transaction->user->nom_util : 'Utilisateur non défini' }}
                </td>
                <td class="border px-4 py-2">{{ $transaction->motif }}</td>
                <td class="border px-4 py-2">{{ number_format($transaction->montant_trans, 2) }} FCFA</td>
                <td class="border px-4 py-2">{{ $transaction->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg mt-6 p-4">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Statistiques des Transactions</h2>
    <canvas id="transactionChart"></canvas>
</div>
<canvas id="transactionsChart"></canvas>
<script>
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($transactionDates),
            datasets: [{
                label: 'Montant des Transactions',
                data: @json($transactionAmounts),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    var ctx2 = document.getElementById('transactionsChart').getContext('2d');
    var chart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: @json($type_transactions->pluck('nom_type_transa')),
            datasets: [{
                label: 'Montant Total',
                data: @json($type_transactions->pluck('total')),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },


        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>