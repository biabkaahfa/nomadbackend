
@extends('back.app')

@section('title',isset($profil) ? 'Modifier un profil' : 'Ajouter un profil')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">
                    {{ isset($profil) && $profil ? 'Modifier' : 'Ajouter' }} Profil
                </h4>
                 {{-- <h2> @if($profil) Modifier  @else Ajouter @endif  Profil</h2> --}}

                {{-- <a href="{{ route('profils.create') }}" class="btn btn-primary float-right viewbutton">@if($profil) Modifier  @else Ajouter @endif Profil</a> --}}
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body booking_card">
                    <div class="table-responsive">
<body>

    {{-- <h2> @if($profil) Modifier  @else Ajouter @endif  Profil</h2> --}}

   <form action="{{ isset($profil) ? route('profils.update', $profil->id) : route('profils.store') }}" method="POST">

    @csrf
    @if (isset($profil))
        @method('PUT')
    @endif

    <!-- Name + Description -->
    <input type="text" name="name" placeholder="Nom du profil" value="{{ old('name', $profil->name ?? '') }}" required>
    <input type="text" name="description" placeholder="Description" value="{{ old('description', $profil->description ?? '') }}">

    <!-- Permissions -->
    <h4>Permissions associées</h4>
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

    <button type="submit"> {{ isset($profil) && $profil ? 'Modifier' : 'Ajouter' }} Profil</button>

</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
