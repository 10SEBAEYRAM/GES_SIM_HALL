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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert" id="info-alert">
            {{ session('info') }}
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
                    <td>
                        <form action="{{ route('users.update', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nom_utili" value="{{ $user->nom_utili }}" class="form-control" required disabled>
                    </td>
                    <td>
                            <input type="text" name="prenom_utili" value="{{ $user->prenom_utili }}" class="form-control" required disabled>
                    </td>
                    <td>
                            <input type="email" name="email_utili" value="{{ $user->email_utili }}" class="form-control" required disabled>
                    </td>
                    <td>
                            <input type="text" name="num_utili" value="{{ $user->num_utili }}" class="form-control" required disabled>
                    </td>
                    <td>
                            <button type="button" class="btn btn-secondary edit-button" data-user-id="{{ $user->id }}">Modifier</button>
                            <button type="submit" class="btn btn-secondary save-button d-none">Sauvegarder</button>
                        </form>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
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
        const errorAlert = document.getElementById('error-alert');
        const infoAlert = document.getElementById('info-alert');
        
        // Fonction pour masquer les alertes après 5 secondes
        const hideAlert = (alert) => {
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('d-none'); // Cacher l'alerte
                }, 5000); 
            }
        }

        hideAlert(successAlert);
        hideAlert(errorAlert);
        hideAlert(infoAlert);

        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(event) {
                if (!confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
                    event.preventDefault();
                }
            });
        });

        // Fonctionnalité d'édition
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                const row = button.closest('tr');
                const inputs = row.querySelectorAll('input');

                inputs.forEach(input => {
                    input.disabled = false; // Activer les champs de saisie
                });

                button.classList.add('d-none'); // Cacher le bouton Modifier
                row.querySelector('.save-button').classList.remove('d-none'); // Afficher le bouton Sauvegarder
            });
        });
    });
</script>

<style>
    /* Conteneur principal */
   

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

    /* Alertes */
    .alert {
        position: relative; 
        z-index: 1; 
        margin-bottom: 20px; 
        border-radius: 5px; /* Ajout d'une bordure arrondie */
        padding: 15px; /* Espacement intérieur */
    }

    .alert-success {
        background-color: #28a745; /* Fond vert */
        color: white; 
    }

    .alert-danger {
        background-color: #dc3545; /* Fond rouge */
        color: white; 
    }

    .alert-info {
        background-color: #007bff; /* Fond bleu */
        color: white; 
    }

    /* Cacher l'alerte */
    .d-none {
        display: none;
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
