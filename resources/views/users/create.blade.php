@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Ajouter un nouvel utilisateur</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="form-group">
            <label for="nom_utili">Nom</label>
            <input type="text" name="nom_utili" class="form-control" value="{{ old('nom_utili') }}" required>
            @error('nom_utili')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="prenom_utili">Prénom</label>
            <input type="text" name="prenom_utili" class="form-control" value="{{ old('prenom_utili') }}" required>
            @error('prenom_utili')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email_utili">Email</label>
            <input type="email" name="email_utili" class="form-control" value="{{ old('email_utili') }}" required>
            @error('email_utili')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="num_utili">Numéro de téléphone</label>
            <input type="text" name="num_utili" class="form-control" value="{{ old('num_utili') }}" required>
            @error('num_utili')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<style>
    /* Styles CSS comme précédemment */
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px;
        width: 100%;
        box-sizing: border-box;
    }

    .text-danger {
        font-size: 0.9em;
        margin-top: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s;
        width: 100%;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .alert-success {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>
@endsection
