@extends('layouts.app')

@section('title', 'Créer une Caisse')

@section('content')
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Créer une Nouvelle Caisse</h1>

            <form action="{{ route('caisses.store') }}" method="POST">
                @csrf
                <!-- Nom de la Caisse -->
                <div class="mb-6">
                    <label for="nom_caisse" class="block text-sm font-medium text-gray-700">Nom de la Caisse</label>
                    <input type="text" id="nom_caisse" name="nom_caisse" class="form-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <!-- Balance de la Caisse -->
                <div class="mb-6">
                    <label for="balance_caisse" class="block text-sm font-medium text-gray-700">Balance de la Caisse</label>
                    <input type="number" id="balance_caisse" name="balance_caisse" class="form-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('caisses.index') }}" class="inline-block text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-md focus:outline-none">
                        Annuler
                    </a>

                    <button type="submit" class="inline-block text-sm bg-blue-600 text-white hover:bg-blue-700 px-6 py-2 rounded-md focus:outline-none">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
