@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Nouveau type de transaction</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('type-transactions.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="nom_type_transa" value="{{ old('nom_type_transa') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                {{-- <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description_type_trans" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                              rows="3">{{ old('description_type_trans') }}</textarea>
                </div> --}}

                <div class="flex justify-end gap-4">
                    <a href="{{ route('type-transactions.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 