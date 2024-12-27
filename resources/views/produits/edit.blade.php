@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Modifier le Produit</h2>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('produits.update', $produit->id_prod) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nom du produit</label>
                        <input type="text" name="nom_prod" value="{{ old('nom_prod', $produit->nom_prod) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Balance</label>
                        <input type="number" name="balance" value="{{ old('balance', $produit->balance) }}" step="0.01"
                            min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" value="1"
                                {{ old('status', $produit->status) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2">Actif</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('produits.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
