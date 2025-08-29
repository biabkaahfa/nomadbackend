
@extends('back.app')
@section('title', 'Parametres')
@section('dashboard-header')
<h4 class="mt-5">personaliser </h4>
 {{-- <a href="{{ route('notifications.create') }}" class="btn btn-primary float-right viewbutton">Envoyer  une Notifications</a> --}}
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        @if($parametre->logo)
            <img src="{{ asset('storage/' . $parametre->logo) }}" alt="Logo" style="height: 100px;">
        @endif

        <p><strong>Slogan :</strong> {{ $parametre->slogan }}</p>
        <p><strong>Couleur principale :</strong> <span style="background-color: {{ $parametre->couleur_principale }}; padding: 5px 10px;">{{ $parametre->couleur_principale }}</span></p>
        <p><strong>Couleur secondaire :</strong> <span style="background-color: {{ $parametre->couleur_secondaire }}; padding: 5px 10px;">{{ $parametre->couleur_secondaire }}</span></p>
    </div>

    </div>
</div>
@endsection
