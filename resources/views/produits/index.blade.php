@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 border border-gray-300">
            <div class="flex justify-between items-center mb-6 border-b border-gray-300 pb-4">
                <h2 class="text-2xl font-bold">Produits</h2>
                <a href="{{ route('produits.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md border border-blue-700 transition duration-300 ease-in-out">
                    Nouveau Produit
                </a>
            </div>

            {{-- Message de succès --}}
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-md mb-4 shadow-md border border-green-400">
                {{ session('success') }}
            </div>
            @endif

            {{-- Message d'erreur --}}
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-md mb-4 shadow-md border border-red-400">
                {{ session('error') }}
            </div>
            @endif

            {{-- Tableau des produits --}}
            <div class="border border-gray-300 rounded-md shadow-sm overflow-hidden mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Balance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($produits as $produit)
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                                {{ $produit->nom_prod }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                                {{ number_format($produit->balance, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $produit->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $produit->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium border-b border-gray-300">
                                <a href="{{ route('produits.edit', $produit->id_prod) }}"
                                    class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition duration-200 border border-blue-700 mr-3">
                                    Modifier
                                </a>

                                <form action="{{ route('produits.destroy', $produit->id_prod) }}"
                                    method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md transition duration-200 border border-red-700"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 border-t border-gray-300 pt-4 ">
                {{ $produits->links() }}
            </div>
        </div>
    </div>
</div>


@endsection