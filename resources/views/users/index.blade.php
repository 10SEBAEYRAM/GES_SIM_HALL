@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Liste des Utilisateurs</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Ajouter un Utilisateur</a>
    </div>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Numéro de téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->nom_utili }}</td>
                    <td>{{ $user->prenom_utili }}</td>
                    <td>{{ $user->email_utili }}</td>
                    <td>{{ $user->num_utili }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary">Modifier</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun utilisateur trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }} 
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
            }, 5000); 
        }

        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(event) {
                if (!confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
                    event.preventDefault();
                }
            });
        });
    });
</script>






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

    /* Tableaux */
    .table {
        width: 100%; 
        border-collapse: collapse; 
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6; 
        padding: 12px; 
        text-align: left; 
    }

    .table th {
        background-color: #f8f9fa; 
        font-weight: bold; 
    }

    /* Bouton "Ajouter un Utilisateur" */
    .btn-primary {
        background-color: #007bff; 
        color: white; 
        padding: 10px 15px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        text-decoration: none; 
        transition: background-color 0.3s; 
    }

    .btn-primary:hover {
        background-color: #0056b3; 
    }

    /* Boutons d'action dans la table */
    .btn-secondary {
        background-color: #6c757d; 
        color: white; 
        padding: 5px 10px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        text-decoration: none; 
        transition: background-color 0.3s; 
    }

    .btn-secondary:hover {
        background-color: #5a6268; 
    }

    /* Bouton de suppression */
    .btn-danger {
        background-color: #dc3545; 
        color: white; 
        padding: 5px 10px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        text-decoration: none; 
        transition: background-color 0.3s; 
    }

    .btn-danger:hover {
        background-color: #c82333; 
    }
</style>
@endsection
