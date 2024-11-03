@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Types de Transaction</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('type-transactions.create') }}" class="btn btn-primary mb-3">
        Nouveau Type
    </a>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($typeTransactions as $type)
                        <tr>
                            <td>{{ $type->nom_type_transa }}</td>
                            <td>{{ $type->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('type-transactions.edit', $type) }}" 
                                   class="btn btn-sm btn-primary">Modifier</a>
                                
                                <form action="{{ route('type-transactions.destroy', $type) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Êtes-vous sûr ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $typeTransactions->links() }}
        </div>
    </div>
</div>
@endsection