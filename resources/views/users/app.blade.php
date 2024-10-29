<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title> <!-- Affiche le titre dynamique -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/create.css') }}"> 
    <link rel="stylesheet" href="{{ asset('/css/index.css') }}"> 
    <!-- Inclure le CSS de Bootstrap dans le fichier layouts/app.blade.php -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQp3Fz0f6W1zWYBupY5gfbD5S5UJp6i6Yv5eKk7/f8x1JrD6eE" crossorigin="anonymous">
    <!-- Lien CSS de Bootstrap dans la section <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ENjdO4Dr2bkBIFxQp3Fz0f6W1zWYBupY5gfbD5S5UJp6i6Yv5eKk7/f8x1JrD6eE" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>


  <!-- Autres balises comme favicon, styles supplémentaires, etc. -->
</head>
<body>
    <header>
       
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Accueil</a></li>
                <li><a href="{{ route('users.index') }}">Utilisateurs</a></li>
            </ul>
        </nav>
    </header>

    <main>
        
        <div class="container">
            @yield('content') 
        </div>
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Mon Application. Tous droits réservés.</p>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBogGzS9Gc/qMnl6U5ofb2VI5uFnVajDq4pbb4c02JwAoKTp" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-OERcA2zJG+bJ7V0f9FEeFgK3lrqz8P4B8NHZj+AK4jl9OBOdyl8k3h5LG5ozp3bc" crossorigin="anonymous"></script>

</body>
</html>
