@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-8 py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Créer un utilisateur</h2>

        {{-- Messages d'erreurs --}}
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <ul>
                    @foreach($errors->all() as $error)  
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            {{-- Nom --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="nom_util" value="{{ old('nom_util') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Prénom --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Prénom</label>
                <input type="text" name="prenom_util" value="{{ old('prenom_util') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email_util" value="{{ old('email_util') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Téléphone --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                <input type="text" name="num_util" value="{{ old('num_util') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Adresse --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <input type="text" name="adress_util" value="{{ old('adress_util') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Mot de passe --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            {{-- Type d'utilisateur --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Type d'utilisateur</label>
                <select name="type_users_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Sélectionnez un type</option>
                    @foreach($typeUsers as $type)
                        <option value="{{ $type->id_type_users }}" 
                            {{ old('type_users_id') == $type->id_type_users ? 'selected' : '' }}>
                            {{ $type->nom_type_users }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Boutons --}}
            <div class="flex justify-end gap-4">
                <a href="{{ route('users.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">Annuler</a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection
