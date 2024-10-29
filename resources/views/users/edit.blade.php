@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier l'Utilisateur</h2>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nom_utili">Nom</label>
            <input type="text" name="nom_utili" value="{{ $user->nom_utili }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="prenom_utili">Prénom</label>
            <input type="text" name="prenom_utili" value="{{ $user->prenom_utili }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email_utili">Email</label>
            <input type="email" name="email_utili" value="{{ $user->email_utili }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="num_utili">Numéro de téléphone</label>
            <input type="text" name="num_utili" value="{{ $user->num_utili }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
