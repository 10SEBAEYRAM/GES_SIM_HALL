@extends('layouts.app')

@section('content')
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Information du profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Mise à jour des informations de profil et de l'adresse électronique de votre compte.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        
        @if (session('status'))
            <div class="mt-2 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-2 text-sm text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <x-input-label for="nom_utili" :value="__('Nom')" />
            <x-text-input id="nom_utili" name="nom_utili" type="text" class="mt-1 block w-full" :value="old('nom_utili', $user->nom_utili)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('nom_utili')" />
        </div>

        <div>
            <x-input-label for="email_utili" :value="__('Email')" />
            <x-text-input id="email_utili" name="email_utili" type="email" class="mt-1 block w-full" :value="old('email_utili', $user->email_utili)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email_utili')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">{{ __('Sauvegardé.') }}</p>
            @endif
        </div>
    </form>
</section>
@endsection
