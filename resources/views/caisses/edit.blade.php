@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Modifier la Caisse</h1>

    <form action="{{ route('caisses.update', $caisse->id_caisse) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nom_caisse" class="block text-gray-700 text-sm font-bold mb-2">Nom de la Caisse</label>
            <input type="text" name="nom_caisse" id="nom_caisse" value="{{ $caisse->nom_caisse }}" class="form-input w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label for="balance_caisse" class="block text-gray-700 text-sm font-bold mb-2">Balance de la Caisse</label>
            <input type="number" name="balance_caisse" id="balance_caisse" value="{{ $caisse->balance_caisse }}" class="form-input w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Mettre à jour</button>
        </div>
    </form>
@endsection
