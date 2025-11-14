@extends('back.app')

@section('title', isset($typeAbonement) ? 'Modifier Type' : 'Créer Type')

@section('dashboard-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ isset($typeAbonement) ? 'Modifier' : 'Créer' }} un Type d'Abonnement
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($typeAbonement) ? route('type-abonements.update', $typeAbonement) : route('type-abonements.store') }}" method="POST">
                @csrf
                @if(isset($typeAbonement))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $typeAbonement->nom ?? '') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Libellé *</label>
                            <input type="text" name="libelle" class="form-control @error('libelle') is-invalid @enderror"
                                   value="{{ old('libelle', $typeAbonement->libelle ?? '') }}" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type de Compagnie *</label>
                            <select name="type_compagnie" class="form-control @error('type_compagnie') is-invalid @enderror" required>
                                <option value="">Sélectionner...</option>
                                <option value="privee" {{ old('type_compagnie', $typeAbonement->type_compagnie ?? '') == 'privee' ? 'selected' : '' }}>Privée</option>
                                <option value="publique" {{ old('type_compagnie', $typeAbonement->type_compagnie ?? '') == 'publique' ? 'selected' : '' }}>Publique</option>
                            </select>
                            @error('type_compagnie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prix Mensuel (FCFA) *</label>
                            <input type="number" step="0.01" name="prix_mensuel" class="form-control @error('prix_mensuel') is-invalid @enderror"
                                   value="{{ old('prix_mensuel', $typeAbonement->prix_mensuel ?? 0) }}" required>
                            @error('prix_mensuel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Limite Notifications/Jour *</label>
                            <input type="number" name="limite_notifications" class="form-control @error('limite_notifications') is-invalid @enderror"
                                   value="{{ old('limite_notifications', $typeAbonement->limite_notifications ?? 0) }}" min="-1">
                            <small class="form-text text-muted">Mettre -1 pour illimité</small>
                            @error('limite_notifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Commission Sur Place (FCFA) *</label>
                            <input type="number" step="0.01" name="commission_sur_place" class="form-control @error('commission_sur_place') is-invalid @enderror"
                                   value="{{ old('commission_sur_place', $typeAbonement->commission_sur_place ?? 0) }}" required>
                            @error('commission_sur_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Commission En Ligne (FCFA) *</label>
                            <input type="number" step="0.01" name="commission_en_ligne" class="form-control @error('commission_en_ligne') is-invalid @enderror"
                                   value="{{ old('commission_en_ligne', $typeAbonement->commission_en_ligne ?? 0) }}" required>
                            @error('commission_en_ligne')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="acces_notes" value="1"
                                       class="form-check-input @error('acces_notes') is-invalid @enderror"
                                       {{ old('acces_notes', $typeAbonement->acces_notes ?? false) ? 'checked' : '' }} id="acces_notes">
                                <label class="form-check-label" for="acces_notes">Accès aux Notes</label>
                                @error('acces_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="est_actif" value="1"
                                       class="form-check-input @error('est_actif') is-invalid @enderror"
                                       {{ old('est_actif', $typeAbonement->est_actif ?? true) ? 'checked' : '' }} id="est_actif">
                                <label class="form-check-label" for="est_actif">Actif</label>
                                @error('est_actif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $typeAbonement->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($typeAbonement) ? 'Mettre à jour' : 'Créer' }}
                    </button>
                    <a href="{{ route('type-abonements.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
