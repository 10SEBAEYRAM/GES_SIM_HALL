@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier l'Utilisateur</h2>
    <form action="{{ route('users.update', $user->id) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nom_utili">Nom</label>
            <input type="text" name="nom_utili" value="{{ old('nom_utili', $user->nom_utili) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="prenom_utili">Prénom</label>
            <input type="text" name="prenom_utili" value="{{ old('prenom_utili', $user->prenom_utili) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email_utili">Email</label>
            <input type="email" name="email_utili" value="{{ old('email_utili', $user->email_utili) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="num_utili">Numéro de téléphone</label>
            <input type="text" name="num_utili" value="{{ old('num_utili', $user->num_utili) }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>

<style>
    /* Conteneur principal */
    .container {
        max-width: 800px; 
        margin: 0 auto; 
        padding: 20px; 
        background-color: #fff;
        border-radius: 8px; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
    }

    /* Titre */
    h2 {
        font-size: 24px; 
        margin-bottom: 20px; 
    }

    /* Formulaire */
    .form-group {
        margin-bottom: 15px; 
    }

    label {
        font-weight: bold; 
        margin-bottom: 5px; 
    }

    .form-control {
        border: 1px solid #ced4da; 
        border-radius: 5px; 
        padding: 10px; 
        transition: border-color 0.3s; 
    }

    .form-control:focus {
        border-color: #007bff; /* Couleur de bordure au focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25); /* Ombre au focus */
    }

    /* Bouton */
    .btn-primary {
        background-color: #007bff; 
        color: white; 
        padding: 10px 15px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        transition: background-color 0.3s; 
    }

    .btn-primary:hover {
        background-color: #0056b3; 
    }
</style>
@endsection
