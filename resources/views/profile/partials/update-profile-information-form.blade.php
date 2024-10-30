<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nom_utili" :value="__('Nom')" />
            <x-text-input 
                id="nom_utili" 
                name="nom_utili" 
                type="text" 
                class="mt-1 block w-full" 
                :value="old('nom_utili', $user->nom_utili)" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('nom_utili')" />
        </div>

        <div>
            <x-input-label for="email_utili" :value="__('Email')" />
            <x-text-input 
                id="email_utili" 
                name="email_utili" 
                type="email" 
                class="mt-1 block w-full" 
                :value="old('email_utili', $user->email_utili)" 
                required 
                autocomplete="username" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('email_utili')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
