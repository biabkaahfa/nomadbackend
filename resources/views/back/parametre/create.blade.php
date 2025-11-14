


@extends('back.app')
@section('title', 'Parametres')
@section('dashboard-header')
<h4 class="mt-5">personaliser </h4>
 {{-- <a href="{{ route('notifications.create') }}" class="btn btn-primary float-right viewbutton">Envoyer  une Notifications</a> --}}
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
<form action="{{ route('parametres.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="logo" />
    <input type="text" name="couleur_principale" placeholder="Couleur principale" required />
    <input type="text" name="couleur_secondaire" placeholder="Couleur secondaire" required />
    <input type="text" name="slogan" placeholder="Slogan" />
    <button type="submit">Enregistrer</button>
</form>

    </div>
</div>
@endsection
