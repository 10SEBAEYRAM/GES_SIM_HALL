@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-300 shadow-sm sm:rounded-lg p-6">
            {{-- En-tête avec Titre et Bouton --}}
            <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-300">
                <h2 class="text-2xl font-bold text-gray-800">Transactions</h2>
                <a href="{{ route('transactions.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 border border-blue-600 transition duration-200 font-semibold">
                    Nouvelle Transaction
                </a>
            </div>

            {{-- Alertes --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded-md shadow-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded-md shadow-md mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Onglets des produits -->
            <div class="mb-4 border-b border-gray-300">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="produitTabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 rounded-t-lg border-b-2 border-blue-500 text-blue-600"
                                id="all-tab"
                                data-tabs-target="#all"
                                type="button"
                                role="tab"
                                aria-controls="all"
                                aria-selected="true">
                            Toutes les transactions
                        </button>
                    </li>
                    @foreach($produits as $index => $produit)
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:border-gray-300 border-b"
                                    id="produit-{{ $produit->id_prod }}-tab"
                                    data-tabs-target="#produit-{{ $produit->id_prod }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="produit-{{ $produit->id_prod }}"
                                    aria-selected="false">
                                {{ $produit->nom_prod }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contenu des onglets -->
            <div id="produitTabContent" class="border border-gray-300 rounded-md">
                <!-- Toutes les transactions -->
                <div class="block p-4" id="all" role="tabpanel" aria-labelledby="all-tab">
                    @include('transactions._table', ['transactions' => $transactions])
                </div>

                <!-- Transactions par produit -->
                @foreach($produits as $produit)
                    <div class="hidden p-4" 
                         id="produit-{{ $produit->id_prod }}" 
                         role="tabpanel"
                         aria-labelledby="produit-{{ $produit->id_prod }}-tab">
                        @include('transactions._table', ['transactions' => $transactions->where('produit_id', $produit->id_prod)])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let tabButtons = document.querySelectorAll('[role="tab"]');
    let tabPanels = document.querySelectorAll('[role="tabpanel"]');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Désactiver tous les onglets
            tabButtons.forEach(btn => {
                btn.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
                btn.classList.add('hover:text-gray-600', 'hover:border-gray-300');
                btn.setAttribute('aria-selected', 'false');
            });

            // Cacher tous les panneaux
            tabPanels.forEach(panel => {
                panel.classList.add('hidden');
            });

            // Activer l'onglet cliqué
            button.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            button.classList.remove('hover:text-gray-600', 'hover:border-gray-300');
            button.setAttribute('aria-selected', 'true');

            // Afficher le panneau correspondant
            const panelId = button.getAttribute('data-tabs-target');
            document.querySelector(panelId).classList.remove('hidden');
        });
    });
});
</script>
@endpush
@endsection
