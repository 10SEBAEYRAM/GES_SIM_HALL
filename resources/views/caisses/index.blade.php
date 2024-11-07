@extends('layouts.app')

@section('title', 'Liste des Caisses')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <div class="flex justify-between items-center mb-8 border-b pb-4 border-gray-300">
                <h2 class="text-2xl font-bold text-gray-800">Liste des Caisses</h2>
                <a href="{{ route('caisses.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md border border-blue-700 transition duration-300 ease-in-out">
                    Créer une nouvelle Caisse
                </a>
            </div>

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tableau des caisses --}}
            <table class="min-w-full divide-y divide-gray-200 mb-8 bg-white border border-gray-300 rounded-lg overflow-hidden shadow-md">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nom de la Caisse
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Balance
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($caisses as $caisse)
                        <tr class="hover:bg-gray-50 transition duration-200 border-b border-gray-300">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $caisse->nom_caisse }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ number_format($caisse->balance_caisse, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                {{-- Lien pour modifier --}}
                                <a href="{{ route('caisses.edit', $caisse->id_caisse) }}" 
                                   class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition duration-200 border border-blue-700 mr-3">
                                    Modifier
                                </a>

                                {{-- Formulaire pour supprimer --}}
                                <form action="{{ route('caisses.destroy', $caisse->id_caisse) }}" 
                                      method="POST" 
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md transition duration-200 border border-red-700"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-8 border-t pt-4 border-gray-300">
                <div class="flex justify-between items-center">
                    {{ $caisses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
