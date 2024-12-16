@extends('layouts.app')

@section('title', 'Créer une Caisse')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Créer une Nouvelle Caisse</h1>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation:</h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('caisses.store') }}" method="POST">
            @csrf
            <!-- Nom de la Caisse -->
            <div class="mb-6">
                <label for="nom_caisse" class="block text-sm font-medium text-gray-700">Nom de la Caisse</label>
                <input type="text"
                    id="nom_caisse"
                    name="nom_caisse"
                    value="{{ old('nom_caisse') }}"
                    class="form-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nom_caisse') border-red-500 @enderror"
                    required>
                @error('nom_caisse')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Balance de la Caisse -->
            <div class="mb-6">
                <label for="balance_caisse" class="block text-sm font-medium text-gray-700">Balance de la Caisse</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number"
                        id="balance_caisse"
                        name="balance_caisse"
                        value="{{ old('balance_caisse') }}"
                        step="0.01"
                        min="0"
                        class="form-input block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('balance_caisse') border-red-500 @enderror"
                        required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">FCFA</span>
                    </div>
                </div>
                @error('balance_caisse')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('caisses.index') }}"
                    class="inline-block text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-md focus:outline-none">
                    Annuler
                </a>

                <button type="submit"
                    class="inline-block text-sm bg-blue-600 text-white hover:bg-blue-700 px-6 py-2 rounded-md focus:outline-none">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection