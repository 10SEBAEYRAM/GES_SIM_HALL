@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des utilisateurs</h1>

    <!-- Affichage du nombre total d'utilisateurs -->
    <div class="mb-4">
        <strong>Total des utilisateurs :</strong> {{ $totalUsers }}
    </div>

    <!-- Table des utilisateurs -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Numéro</th>
                <th>Adresse</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id_util }}</td>
                <td>{{ $user->nom_util }}</td>
                <td>{{ $user->prenom_util }}</td>
                <td>{{ $user->email_util }}</td>
                <td>{{ $user->num_util }}</td>
                <td>{{ $user->adress_util }}</td>
                <td>{{ $user->typeUser->type_name }}</td> <!-- Assurez-vous que le modèle 'User' a une relation avec 'TypeUser' -->
                <td>
                    <a href="{{ route('users.edit', $user->id_util) }}" class="btn btn-primary btn-sm">Modifier</a>
                    <form action="{{ route('users.destroy', $user->id_util) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection