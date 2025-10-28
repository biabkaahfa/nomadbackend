@extends('back.app')
@section('title',"Modifier une personnalisation de carte")

@section('dashboard-header')
<h3 class="page-title mt-5">Modifier la personnalisation</h3>
@endsection

@section('dashboard-content')
<div class="container mt-4">
    <form action="{{ route('personalisationCard.update', $perso->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Pays</label>
            <input type="text" name="pays" value="{{ old('pays', $perso->pays) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Devise</label>
            <input type="text" name="devise" value="{{ old('devise', $perso->devise) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Numéro</label>
            <input type="text" name="numero" value="{{ old('numero', $perso->numero) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Couleur principale</label>
            <div class="d-flex align-items-center">
                <!-- Color picker -->
                <input type="color" id="colorPicker" value="{{ old('couleur_principale', $perso->couleur_principale) }}" class="form-control form-control-color me-3" style="width: 70px;">

                <!-- Champ texte lié -->
                <input type="text" name="couleur_principale" id="colorValue"
                       value="{{ old('couleur_principale', $perso->couleur_principale) }}"
                       class="form-control" required>
            </div>
            <!-- Aperçu -->
            <div id="colorPreview" class="mt-2 p-3 rounded"
                 style="background-color: {{ old('couleur_principale', $perso->couleur_principale) }}; border:1px solid #ccc;">
                Aperçu couleur
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix</label>
            <input type="number" name="prix" step="0.01" value="{{ old('prix', $perso->prix) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Compagnie</label>
            <select name="idCompagnie" class="form-control" required>
                @foreach($compagnies as $compagnie)
                    <option value="{{ $compagnie->id }}" {{ $compagnie->id == $perso->idCompagnie ? 'selected' : '' }}>
                        {{ $compagnie->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('personalisationCard.indexe') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<!-- Script pour mise à jour dynamique -->
<script>
    const colorPicker = document.getElementById('colorPicker');
    const colorValue = document.getElementById('colorValue');
    const colorPreview = document.getElementById('colorPreview');

    // Quand on change avec le color picker
    colorPicker.addEventListener('input', (e) => {
        colorValue.value = e.target.value;
        colorPreview.style.backgroundColor = e.target.value;
    });

    // Quand on modifie manuellement le champ texte
    colorValue.addEventListener('input', (e) => {
        colorPicker.value = e.target.value;
        colorPreview.style.backgroundColor = e.target.value;
    });
</script>
@endsection
