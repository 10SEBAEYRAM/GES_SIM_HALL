@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Détails de la Transaction</h2>
                <a href="{{ route('transactions.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Retour
                </a>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Informations Générales</h3>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Date/Heure:</span>
                                <span class="font-medium">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Type:</span>
                                <span class="font-medium">{{ $transaction->typeTransaction->nom_type_transa }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Produit:</span>
                                <span class="font-medium">{{ $transaction->produit->nom_prod }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Opérateur:</span>
                                <span class="font-medium">{{ $transaction->user->nom_util }} {{ $transaction->user->prenom_util }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Montants</h3>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Montant:</span>
                                <span class="font-medium">{{ number_format($transaction->montant_trans, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Commission:</span>
                                <span class="font-medium">{{ number_format($transaction->commission_appliquee, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Bénéficiaire:</span>
                                <span class="font-medium">{{ $transaction->num_beneficiaire }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">État de la Transaction</h3>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Statut:</span>
                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full
                                    @if($transaction->statut === 'COMPLETE') bg-green-100 text-green-800
                                    @elseif($transaction->statut === 'ANNULE') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $transaction->statut }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Soldes Produit</h3>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Solde Avant:</span>
                                <span class="font-medium">{{ number_format($transaction->solde_avant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Solde Après:</span>
                                <span class="font-medium">{{ number_format($transaction->solde_apres, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Soldes Caisse</h3>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Solde Avant:</span>
                                <span class="font-medium">{{ number_format($transaction->solde_caisse_avant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Solde Après:</span>
                                <span class="font-medium">{{ number_format($transaction->solde_caisse_apres, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 