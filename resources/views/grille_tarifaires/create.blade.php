@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Nouvelle Grille Tarifaire</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('grille-tarifaires.store') }}" method="POST">
          @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700" for="produit_id">Produit</label>
                    <select name="produit_id" id="produit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id_prod }}" {{ old('produit_id') == $produit->id_prod ? 'selected' : '' }}>
                                {{ $produit->nom_prod }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700" for="montant_min">Montant Minimum</label>
                    <input type="number" id="montant_min" name="montant_min" value="{{ old('montant_min') }}" step="1" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700" for="montant_max">Montant Maximum</label>
                    <input type="number" id="montant_max" name="montant_max" value="{{ old('montant_max') }}" step="1" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700" for="commission_grille_tarifaire">Commission</label>
                    <input type="number" id="commission_grille_tarifaire" name="commission_grille_tarifaire" value="{{ old('commission_grille_tarifaire') }}"
                        step="1" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('grille-tarifaires.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection 