@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Modifier l'utilisateur</h2>

            <form action="{{ route('users.update', ['user' => $user->id_util]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="nom_util" value="{{ old('nom_util', $user->nom_util) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="prenom_util" value="{{ old('prenom_util', $user->prenom_util) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email_util" value="{{ old('email_util', $user->email_util) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input type="text" name="num_util" value="{{ old('num_util', $user->num_util) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" name="adress_util" value="{{ old('adress_util', $user->adress_util) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" name="password" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <p class="text-sm text-gray-500">Laissez vide si vous ne souhaitez pas changer le mot de passe.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Type d'utilisateur</label>
                    <select name="type_users_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @foreach($typeUsers as $type)
                            <option value="{{ $type->id_type_users }}" 
                                {{ old('type_users_id', $user->type_users_id) == $type->id_type_users ? 'selected' : '' }}>
                                {{ $type->nom_type_users }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('users.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
