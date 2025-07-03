<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>@yield('title')</title>

    <!-- Dashboard -Links -->
    @include('back.partials.styles')
</head>


<body>
    <h2>Session : {{ session('success') }}</h2>
    <!-- Main wrapper -->
    <div class="main-wrapper">
        <!-- Début header -->
        @include('back.partials.header')
        <!-- Fin header -->

        {{-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif --}}

        <!-- Alertes iziToast -->
        {{-- <script>
            @if (session('success'))
                iziToast.success({
                    title: 'Succès',
                    message: '{{ session('success') }}',
                    position: 'bottomCenter', // 👈 Ici
                    timeout: 5000
                });
            @endif

            @if (session('error'))
                iziToast.error({
                    title: 'Erreur',
                    message: '{{ session('error') }}',
                    position: 'bottomCenter', // 👈 Ici aussi
                    timeout: 5000
                });
            @endif
        </script> --}}

        <!-- Début sidebar -->
        @include('back.partials.sidebar')
        <!-- Fin sidebar -->

        <!-- Contenu de la page -->
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    @yield('dashboard-header')
                </div>
                @yield('dashboard-content')
            </div>
        </div>
        <!-- Fin contenu de la page -->
    </div>

    <!-- Scripts Dashboard -->
    @include('back.partials.scripts')

    <!-- Point d’insertion pour les scripts enfants -->

    <script>
        console.log(iziToast);

        @if (session('success'))
            iziToast.success({
                title: 'Succès',
                message: '{{ session('success') }}',
                position: 'topRight',
                timeout: 5000
            });
        @endif
        @if (session('error'))
            iziToast.error({
                title: 'Erreur',
                message: '{{ session('error') }}',
                position: 'center',
                timeout: 5000
            });
        @endif
    </script>


    {{-- Push iziToast script
    @push('scripts')
        <script>
            @if (session('success'))
                iziToast.success({
                    title: 'Succès',
                    message: '{{ session('success') }}',
                    position: 'center',
                    timeout: 5000
                });
            @endif

            @if (session('error'))
                iziToast.error({
                    title: 'Erreur',
                    message: '{{ session('error') }}',
                    position: 'center',
                    timeout: 5000
                });
            @endif
        </script>
    @endpush
    @stack('scripts') --}}

    @yield('scripts')
</body>

</html>
