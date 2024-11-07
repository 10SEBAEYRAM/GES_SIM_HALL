@extends('layouts.app')

@section('content')
    <h1>Modifier la Caisse</h1>

    <form action="{{ route('caisses.update', $caisse->id_caisse) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="nom_caisse" class="block">Nom de la Caisse</label>
        <input type="text" name="nom_caisse" id="nom_caisse" value="{{ $caisse->nom_caisse }}" class="form-input">
    </div>

    <div class="mb-4">
        <label for="balance_caisse" class="block">Balance de la Caisse</label>
        <input type="number" name="balance_caisse" id="balance_caisse" value="{{ $caisse->balance_caisse }}" class="form-input">
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </div>
</form>

@endsection
