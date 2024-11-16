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




            {{-- Affichage des messages de succès et d'erreur --}}

            <div class="flex-1 overflow-auto p-6 bg-gray-50">
                {{-- @yield('content') --}}
    
                {{-- Alertes --}}
                @if(session()->has('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
    
                @if(session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Affichage des Balances des Produits --}}
           {{-- Affichage des Balances des Produits --}}
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Balances des Produits</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($produits as $index => $produit)
            <div class="p-4 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between" 
                 style="background-color: {{ ['#e0f7fa', '#ffebee', '#fff3e0', '#e8f5e9'][$index % 4] }}">
                
                <div>
                    <h4 class="text-base font-semibold text-gray-800">{{ $produit->nom_prod }}</h4>
                    <p class="text-sm text-gray-500">Balance Actuelle</p>
                </div>

                <div class="text-lg font-bold text-gray-900">
                    {{ number_format($produit->balance, 0, ',', ' ') }} FCFA
                </div>
            </div>
        @endforeach
    </div>
</div>


            {{-- Onglets des produits --}}
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
   

    // Activer le premier onglet et panneau par défaut
    if (tabButtons.length > 0) {
        tabButtons[0].classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
        tabButtons[0].setAttribute('aria-selected', 'true');
    }
    if (tabPanels.length > 0) {
        tabPanels[0].classList.remove('hidden');
    }

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
