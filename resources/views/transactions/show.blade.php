@extends('layouts.app')

@section('content')
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Détails de la Transaction</h2>
                <a href="{{ route('transactions.index') }}" 
                   class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-300 transform hover:scale-105">
                    Retour
                </a>
            </div>

            <!-- Tableau des détails de la transaction -->
            <div class="overflow-x-auto bg-white shadow-2xl rounded-lg border border-gray-300">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-indigo-50 text-indigo-700">
                            <th class="px-8 py-5 text-left font-semibold text-lg">Catégorie</th>
                            <th class="px-8 py-5 text-left font-semibold text-lg">Informations</th>
                            <th class="px-8 py-5 text-left font-semibold text-lg">Catégorie</th>
                            <th class="px-8 py-5 text-left font-semibold text-lg">Informations</th>
                            <th class="px-8 py-5 text-left font-semibold text-lg">Catégorie</th>
                            <th class="px-8 py-5 text-left font-semibold text-lg">Informations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="transition duration-300 ease-in-out hover:bg-indigo-100">
                            <td class="px-8 py-4 text-gray-600">Date/Heure</td>
                            <td class="px-8 py-4 font-medium">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-8 py-4 text-gray-600">Produit</td>
                            <td class="px-8 py-4 font-medium">{{ $transaction->produit->nom_prod }}</td>
                            <td class="px-8 py-4 text-gray-600">Opérateur</td>
                            <td class="px-8 py-4 font-medium">{{ $transaction->user->nom_util }} {{ $transaction->user->prenom_util }}</td>
                        </tr>
                        <tr class="transition duration-300 ease-in-out hover:bg-indigo-100">
                            <td class="px-8 py-4 text-gray-600">Type</td>
                            <td class="px-8 py-4 font-medium">{{ $transaction->typeTransaction->nom_type_transa }}</td>
                            <td class="px-8 py-4 text-gray-600">Commission</td>
                            <td class="px-8 py-4 font-medium">{{ number_format($transaction->commission_appliquee, 0, ',', ' ') }} FCFA</td>
                            <td class="px-8 py-4 text-gray-600">Montant</td>
                            <td class="px-8 py-4 font-medium">{{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="transition duration-300 ease-in-out hover:bg-indigo-100">
                            <td class="px-8 py-4 text-gray-600">Solde Produit Avant</td>
                            <td class="px-8 py-4 font-medium">{{ number_format($transaction->solde_avant, 0, ',', ' ') }} FCFA</td>
                            <td class="px-8 py-4 text-gray-600">Solde  Produit Après</td>
                            <td class="px-8 py-4 font-medium">
                                @if($transaction->typeTransaction->nom_type_transa === 'Dépôt')
                                    {{ number_format($transaction->solde_avant + $transaction->montant_trans + $transaction->commission_appliquee, 0, ',', ' ') }} FCFA
                                @elseif($transaction->typeTransaction->nom_type_transa === 'Retrait')
                                    {{ number_format($transaction->solde_avant - $transaction->montant_trans + $transaction->commission_appliquee, 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($transaction->solde_avant + $transaction->commission_appliquee, 0, ',', ' ') }} FCFA
                                @endif
                            </td>
                            <td class="px-8 py-4 text-gray-600">Bénéficiaire</td>
                            <td class="px-8 py-4 font-medium">{{ $transaction->num_beneficiaire }}</td>
                        </tr>
                        <tr class="transition duration-300 ease-in-out hover:bg-indigo-100">
                            <td class="px-8 py-4 text-gray-600">Solde Caisse Avant</td>
                            <td class="px-8 py-4 font-medium">{{ number_format($transaction->solde_caisse_avant, 0, ',', ' ') }} FCFA</td>
                            <td class="px-8 py-4 text-gray-600">Solde Caisse Après</td>
                            <td class="px-8 py-4 font-medium">
                                @if($transaction->typeTransaction->nom_type_transa === 'Dépôt')
                                    {{ number_format($transaction->solde_caisse_avant + $transaction->montant_trans, 0, ',', ' ') }} FCFA
                                @elseif($transaction->typeTransaction->nom_type_transa === 'Retrait')
                                    {{ number_format($transaction->solde_caisse_avant - $transaction->montant_trans, 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($transaction->solde_caisse_avant + $transaction->commission_appliquee, 0, ',', ' ') }} FCFA
                                @endif
                            </td>
                            <td class="px-8 py-4 text-gray-600">Statut</td>
                            <td class="px-8 py-4">
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                                    @if($transaction->statut === 'COMPLETE') bg-green-100 text-green-800
                                    @elseif($transaction->statut === 'ANNULE') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $transaction->statut }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
