@extends('layouts.app')

@section('content')
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Details') }}
        </h2>
    </header>

    <div class="mt-6 space-y-4">
        <p><strong>{{ __('Name:') }}</strong> {{ $user->nom_utili }}</p>
        <p><strong>{{ __('Email:') }}</strong> {{ $user->email_utili }}</p>
    </div>

    <div class="mt-6">
        <x-primary-button onclick="location.href='{{ route('profile.edit') }}'">{{ __('Edit Profile') }}</x-primary-button>
    </div>
</section>
@endsection
