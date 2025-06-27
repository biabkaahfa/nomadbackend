@extends('back.app')

@section('title', isset($profil) ? "Modifier un profil" : "Ajouter un profil")

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title mt-5">
                {{ isset($profil) ? 'Modifier' : 'Ajouter' }} un Profil
            </h3>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ isset($profil) ? route('profils.update', $profil) : route('profils.store') }}" method="POST">
                @csrf
                @if (isset($profil))
                    @method('PUT')
                @endif

                <div class="row formtype">
                    <!-- Nom du profil -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom du profil</label>
                            <input class="form-control" type="text" name="name"
                                value="{{ isset($profil) ? old('name', $profil->name) : old('name') }}" required />
                            @error('name')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Description</label>
                            <input class="form-control" type="text" name="description"
                                value="{{ isset($profil) ? old('description', $profil->description) : old('description') }}" />
                            @error('description')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Permissions (si applicable) -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Permissions associées</label>
                            <div class="d-flex flex-wrap">
                                @foreach($permissions as $permission)
                                    <div class="form-check mr-4 mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->id }}"
                                            @if (isset($profil) && $profil->permissions->contains($permission->id)) checked @endif>
                                        <label class="form-check-label">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bouton de validation -->
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($profil) ? 'Mettre à jour' : 'Créer le profil' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
