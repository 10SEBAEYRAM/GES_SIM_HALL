<div class="bg-white shadow rounded-lg mt-6 p-4">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Nouveaux Utilisateurs</h2>
    <ul>
        @foreach ($nouveauxUtilisateurs as $users)
        <li class="text-gray-600">{{ $users->nom_util }} - {{ $users->created_at->format('d/m/Y') }}</li>
        @endforeach
    </ul>
</div>

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
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">Balance des Produits</h2>
        <p class="text-2xl font-bold text-yellow-500">{{ $balanceProduits }}</p>
    </div>

    <!-- Montant de la Caisse -->
    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700">Montant de la Caisse</h2>
        <p class="text-2xl font-bold text-red-500">{{ $montantCaisse }}</p>
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
</script>