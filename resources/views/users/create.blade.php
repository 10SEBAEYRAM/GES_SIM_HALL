@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Ajouter un Utilisateur</h2>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="nom_utili">Nom</label>
            <input type="text" class="form-control" id="nom_utili" name="nom_utili" value="{{ old('nom_utili') }}" required>
        </div>

        <div class="form-group">
            <label for="prenom_utili">Prénom</label>
            <input type="text" class="form-control" id="prenom_utili" name="prenom_utili" value="{{ old('prenom_utili') }}" required>
        </div>

        <div class="form-group">
            <label for="email_utili">Email</label>
            <input type="email" class="form-control" id="email_utili" name="email_utili" value="{{ old('email_utili') }}" required>
        </div>

        <div class="form-group">
            <label for="num_utili">Numéro de téléphone</label>
            <input type="text" class="form-control" id="num_utili" name="num_utili" value="{{ old('num_utili') }}" required>
        </div>

        <div class="form-group">
            <label for="adress_utili">Adresse</label>
            <input type="text" class="form-control" id="adress_utili" name="adress_utili" value="{{ old('adress_utili') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Créer</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            console.log("Form submitted");
            /
        });
    });
</script>

<style>
    .form-group {
        margin-bottom: 1rem;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        user-select: none;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .mt-3 {
        margin-top: 1rem;
    }
</style>
@endsection
