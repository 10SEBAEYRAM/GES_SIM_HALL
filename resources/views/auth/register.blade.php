<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nom -->
        <div>
            <x-input-label for="nom_utili" :value="__('Nom')" />
            <x-text-input id="nom_utili" class="block mt-1 w-full" type="text" name="nom_utili" :value="old('nom_utili')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nom_utili')" class="mt-2" />
        </div>

        <!-- Adresse Email -->
        <div class="mt-4">
            <x-input-label for="email_utili" :value="__('Email')" />
            <x-text-input id="email_utili" class="block mt-1 w-full" type="email" name="email_utili" :value="old('email_utili')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email_utili')" class="mt-2" />
        </div>

        <!-- Mot de passe -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmer le mot de passe -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
