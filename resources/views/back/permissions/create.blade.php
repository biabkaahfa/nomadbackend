@extends('back.app')

@section('title', isset($permission) ? 'Modifier une permission' : 'Créer une permission')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title mt-5">
                {{ isset($permission) ? 'Modifier' : 'Ajouter' }} une permission
            </h3>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ isset($permission) ? route('permissions.update', $permission) : route('permissions.store') }}" method="POST">
                @csrf
                @if (isset($permission))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Nom de la permission</label>
                    <input type="text" class="form-control" name="name"
                        value="{{ isset($permission) ? old('name', $permission->name) : old('name') }}" required>
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($permission) ? 'Mettre à jour' : 'Créer' }}
                </button>

                @if (isset($permission))
                    <a href="{{ route('permissions.destroy', $permission->id) }}"
                        onclick="event.preventDefault(); if(confirm('Supprimer cette permission ?')) document.getElementById('delete-form').submit();"
                        class="btn btn-danger ml-2">
                        Supprimer
                    </a>

                    <form id="delete-form" action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </form>
        </div>
    </div>
@endsection
