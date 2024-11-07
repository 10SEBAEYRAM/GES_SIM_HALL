<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags et styles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mon Application')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Style personnalisé pour les liens et icônes */
        .nav-link {
            font-size: 1.125rem; /* Augmente la taille du texte */
            padding: 0.75rem 1.25rem; /* Ajuste le padding pour des liens plus grands */
            color: black; /* Couleur des liens en noir */
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            background-color: #F0F9EB; /* Change la couleur de fond au survol */
            color: #4C9E2C; /* Change la couleur du texte au survol */
        }

        /* Taille et couleur des icônes par défaut */
        .nav-link i {
            font-size: 1.5rem; /* Augmente la taille des icônes */
            transition: transform 0.3s ease, color 0.3s ease;
        }

        /* Styles spécifiques pour chaque icône */
        .nav-link .fas.fa-users {
            color: #1E3A8A; /* Bleu pour l'icône des utilisateurs */
        }

        .nav-link .fas.fa-exchange-alt {
            color: #F59E0B; /* Jaune pour l'icône des transactions */
        }

        .nav-link .fas.fa-box {
            color: #10B981; /* Vert pour l'icône des produits */
        }

        .nav-link .fas.fa-table {
            color: #6B7280; /* Gris pour l'icône des grilles tarifaires */
        }

        .nav-link .fas.fa-money-bill-wave {
            color: #4B5563; /* Gris foncé pour l'icône des transactions financières */
        }

        .nav-link .fas.fa-cash-register {
            color: #D97706; /* Orange pour l'icône de caisse */
        }

        .nav-link:hover i {
            transform: scale(1.2); /* Agrandit l'icône au survol */
        }

        .nav-link.active {
            background-color: #4C9E2C; /* Couleur de fond pour l'élément actif */
            color: white; /* Texte en blanc pour l'élément actif */
        }

        .nav-link.active i {
            color: white; /* Icône en blanc pour l'élément actif */
        }
    </style>
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar Navigation -->
        <nav x-data="{ open: false }" class="bg-white border-r border-gray-200 w-64 flex flex-col">
            <div class="p-4">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <a href="{{ route('users.index') }}">
                        <x-application-logo class="h-9 w-auto" />
                    </a>
                </div>

                <!-- User Profile -->
                <div class="mb-6 text-center" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-md hover:bg-gray-100">
                        {{ Auth::check() ? Auth::user()->nom_util . ' ' . Auth::user()->prenom_util : 'Invité' }}
                    </button>

                    <div x-show="open" @click.away="open = false" class="mt-2 bg-white border rounded-md shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-edit mr-2"></i> Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex flex-col space-y-2">
                    @foreach ([
                        ['route' => 'users.index', 'icon' => 'fas fa-users', 'label' => 'Utilisateurs'],
                        ['route' => 'type-transactions.index', 'icon' => 'fas fa-exchange-alt', 'label' => 'Types de Transactions'],
                        ['route' => 'produits.index', 'icon' => 'fas fa-box', 'label' => 'Produits'],
                        ['route' => 'grille-tarifaires.index', 'icon' => 'fas fa-table', 'label' => 'Grilles Tarifaires'],
                        ['route' => 'transactions.index', 'icon' => 'fas fa-money-bill-wave', 'label' => 'Transactions'],
                        ['route' => 'caisses.index', 'icon' => 'fas fa-cash-register', 'label' => 'Caisses'],  
                    ] as $item)
                        <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])"
                            class="nav-link flex items-center rounded-md hover:bg-gray-100">
                            <i class="{{ $item['icon'] }} mr-3"></i>
                            {{ __($item['label']) }}
                        </x-nav-link>
                    @endforeach
                </div>

            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-auto p-6 bg-gray-50">
            @yield('content')

            {{-- Alertes --}}
            @if(session()->has('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
