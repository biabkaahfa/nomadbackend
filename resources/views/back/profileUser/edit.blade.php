@extends('back.app')

<br><br>
@section('title', 'Dashboard - profile')
@section('dashboard-header')
    <div class="row">
        <div class="col">
            <h3 class="page-title">Profile</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">
                    Profile
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-md-12">
            <div class="profile-header">
                <div class="row align-items-center">
                    <div class="col-auto profile-image">
                        <a href="#">
                            <img class="rounded-circle" alt="User Image"
                                 src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('back_auth/assets/img/logo.png') }}">
                        </a>
                    </div>
                    <div class="col ml-md-n2 profile-user-info">
                        <h4 class="user-name mb-3">{{ Auth::user()->name }}</h4>
                        <h6 class="text-muted mt-1">{{ Auth::user()->profil->name }}</h6>
                    </div>
                </div>
            </div>

            <div class="profile-menu">
                <ul class="nav nav-tabs nav-tabs-solid">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#per_details_tab">A propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#password_tab">Mot de passe</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content profile-tab-cont">
                <div class="tab-pane fade show active" id="per_details_tab">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between">
                                        <span>Informations Personnelles</span>
                                        <a class="edit-link" data-toggle="modal" href="#edit_personal_details">
                                            <i class="fa fa-edit mr-1"></i>Modifier
                                        </a>
                                    </h5>

                                    <div class="row">
                                        <p class="col-sm-3 text-sm-right mb-0 mb-sm-3">Nom</p>
                                        <p class="col-sm-9">{{ Auth::user()->name }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-sm-3 text-sm-right mb-0 mb-sm-3">Email</p>
                                        <p class="col-sm-9"><a href="#">{{ Auth::user()->email }}</a></p>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="edit_personal_details" aria-hidden="true" role="">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Détails personnels</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        @if (session('status'))
                                            <div class='alert alert-success'>{{ session('status') }}</div>
                                        @endif

                                        <div class="modal-body">
                                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row form-row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label class="h5">Nom</label>
                                                            <input type="text" class="form-control form-control-lg"
                                                                   name="name"
                                                                   value="{{ Auth::user()->name }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label class="h5">Email</label>
                                                            <input type="email" class="form-control form-control-lg"
                                                                   name="email" value="{{ Auth::user()->email }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label class="h5">Photo de profil</label>
                                                            <input type="file" name="image"
                                                                   class="form-control form-control-lg">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div> {{-- Modal --}}
                        </div>
                    </div>
                </div>

                <div id="password_tab" class="tab-pane fade">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Modifier le mot de passe</h5>
                            <div class="row">
                                <div class="col-md-10 col-lg-6">
                                    <form action="{{ route('profile.passwordUpdate') }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label>Ancien mot de passe </label>
                                            <input type="password" name="current_password" class="form-control">
                                            @error('current_password')
                                                <p class="text-red-500 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Nouveau mot de passe </label>
                                            <input type="password" name="password" class="form-control">
                                            @error('password')
                                                <p class="text-red-500 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Confirmer mot de passe</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                            @error('password_confirmation')
                                                <p class="text-red-500 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <button class="btn btn-primary" type="submit">Enregistrer les modifications</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- Password tab --}}
            </div>
        </div>
    </div>
@endsection
