@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-300 shadow-sm sm:rounded-lg p-6">
            {{-- En-tête avec Titre et Bouton --}}
            <div class="flex justify-between items-center mb-6 border-b pb-4 border-gray-300">
                <h2 class="text-2xl font-bold text-gray-800">Types de Transactions</h2>
                <a href="{{ route('type-transactions.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300 ease-in-out font-semibold border border-blue-600">
                    Nouveau Type
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

            {{-- Tableau des Types de Transactions --}}
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Description
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($typeTransactions as $type)
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-gray-300">
                                {{ $type->nom_type_transa }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 border-b border-gray-300">
                                {{ $type->description_type_trans }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium border-b border-gray-300">
                                <div class="flex space-x-3">
                                    <a href="{{ route('type-transactions.edit', $type->id_type_transa) }}"
                                        class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded transition duration-200 font-semibold border border-blue-600">
                                        Modifier
                                    </a>

                                    <form action="{{ route('type-transactions.destroy', $type->id_type_transa) }}"
                                        method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded transition duration-200 font-semibold border border-red-600"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de transaction ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 border-t pt-4 border-gray-300">
                {{ $typeTransactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection