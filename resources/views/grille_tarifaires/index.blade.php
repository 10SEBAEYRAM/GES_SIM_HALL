@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Grilles Tarifaires</h2>
                <a href="{{ route('grille-tarifaires.create') }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Nouvelle Grille
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Onglets des produits -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="produitTabs" role="tablist">
                    @foreach($produits as $index => $produit)
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg {{ $index === 0 ? 'border-b-2 border-blue-500 text-blue-600' : 'hover:text-gray-600 hover:border-gray-300' }}"
                                    id="produit-{{ $produit->id_prod }}-tab"
                                    data-tabs-target="#produit-{{ $produit->id_prod }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="produit-{{ $produit->id_prod }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $produit->nom_prod }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contenu des onglets -->
            <div id="produitTabContent">
                @foreach($produits as $index => $produit)
                    <div class="p-4 rounded-lg {{ $index === 0 ? 'block' : 'hidden' }}"
                         id="produit-{{ $produit->id_prod }}"
                         role="tabpanel"
                         aria-labelledby="produit-{{ $produit->id_prod }}-tab">
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montant Min
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montant Max
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Commission
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grilleTarifaires->where('produit_id', $produit->id_prod) as $grille)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ number_format($grille->montant_min, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ number_format($grille->montant_max, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ number_format($grille->commission_grille_tarifaire, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <a href="{{ route('grille-tarifaires.edit', $grille->id_grille_tarifaire) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>
                                            
                                            <form action="{{ route('grille-tarifaires.destroy', $grille->id_grille_tarifaire) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette grille tarifaire ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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