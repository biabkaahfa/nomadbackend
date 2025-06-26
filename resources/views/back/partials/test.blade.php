<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Test iziToast</title>
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css"> --}}
</head>

<body>
    <h1>Test iziToast</h1>
    @include('back.partials.styles';)
    @include('back.partials.scripts')

    {{-- <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script> --}}
    <script>
        @if (session('success'))
            iziToast.success({
                title: 'Succès',
                message: '{{ session('success') }}',
                position: 'topRight',
                timeout: 5000
            });
        @endif
    </script>
</body>

</html>
