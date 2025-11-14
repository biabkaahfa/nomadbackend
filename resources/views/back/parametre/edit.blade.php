
@extends('back.app')
@section('title', 'Parametres')
@section('dashboard-header')
<h4 class="mt-5">personaliser </h4>
 {{-- <a href="{{ route('notifications.create') }}" class="btn btn-primary float-right viewbutton">Envoyer  une Notifications</a> --}}
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
              <form action="{{ route('parametres.update', $parametre) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- Logo --}}
        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label><br>
            @if($parametre->logo)
                <img src="{{ asset('storage/' . $parametre->logo) }}" alt="Logo actuel" style="height: 80px;">
            @endif
            <input type="file" name="logo" id="logo" class="form-control mt-2">
            @error('logo')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Couleur principale --}}
        <div class="mb-3">
            <label for="couleur_principale" class="form-label">Couleur principale</label>
            <input type="color" name="couleur_principale" id="couleur_principale" class="form-control form-control-color" value="{{ old('couleur_principale', $parametre->couleur_principale) }}">
            @error('couleur_principale')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Couleur secondaire --}}
        <div class="mb-3">
            <label for="couleur_secondaire" class="form-label">Couleur secondaire</label>
            <input type="color" name="couleur_secondaire" id="couleur_secondaire" class="form-control form-control-color" value="{{ old('couleur_secondaire', $parametre->couleur_secondaire) }}">
            @error('couleur_secondaire')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Slogan --}}
        <div class="mb-3">
            <label for="slogan" class="form-label">Slogan</label>
            <input type="text" name="slogan" id="slogan" class="form-control" value="{{ old('slogan', $parametre->slogan) }}">
            @error('slogan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>

    </div>
</div>
@endsection
