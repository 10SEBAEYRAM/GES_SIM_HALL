<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mon Application') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Styles et Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar avec largeur réduite -->
        <aside class="w-56 bg-white shadow-lg p-6 fixed inset-y-0">
            @include('layouts.navigation') <!-- Sidebar Navigation -->
        </aside>

        <!-- Zone de contenu principal avec plus de largeur -->
        <div class="flex-1 ml-56 p-8 bg-gray-50 overflow-auto">
            <!-- En-tête de la page -->
            @isset($header)
            <header class="bg-white shadow mb-6 p-6 rounded-md">
                <div class="max-w-full mx-auto">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Contenu de la page -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Déplacé @stack('scripts') ici à la fin du body -->
    @stack('scripts')


    @push('scripts')
<script>
$(document).ready(function() {
    $('#produits-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('produits.data') }}",
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nom', name: 'nom' },
            { data: 'balance', name: 'balance' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
</body>

</html>