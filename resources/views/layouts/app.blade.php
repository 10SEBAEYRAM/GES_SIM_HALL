<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mon Application') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Styles et Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        
        <!-- Sidebar avec largeur réduite -->
        <aside class="w-56 bg-white shadow-lg p-6 fixed inset-y-0">
            @include('layouts.navigation') <!-- Sidebar Navigation -->
        </aside>

        <!-- Zone de contenu principal avec plus de largeur -->
        <div class="flex-1 ml-56 p-8 bg-gray-50 overflow-auto">
            <!-- En-tête de la page -->
            @isset($header)
                <header class="bg-white shadow mb-6 p-6 rounded-md">
                    <div class="max-w-full mx-auto">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Contenu de la page -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
