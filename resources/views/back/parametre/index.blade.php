@extends('back.app')
@section('title', 'Paramètres')

@section('dashboard-header')
<h4 class="mt-5">Personnaliser</h4>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">

        {{-- 🔹 Paramètres globaux (Admin général) --}}
        <h5>Paramètres globaux</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Logo</th>
                    {{-- <th>Couleur principale</th>
                    <th>Couleur secondaire</th> --}}
                    <th>Slogan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($globalParametres as $p)
                    <tr>
                        <td><img src="{{ asset('storage/' . $p->logo) }}" width="60" /></td>
                        {{-- <td style="background: {{ $p->couleur_principale }}">{{ $p->couleur_principale }}</td>
                        <td style="background: {{ $p->couleur_secondaire }}">{{ $p->couleur_secondaire }}</td> --}}
                        <td>{{ $p->slogan }}</td>
                        <td><a href="{{ route('parametres.edit', $p) }}">Modifier</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">Aucun paramètre global défini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <hr>

        {{-- 🔹 Paramètres par compagnie --}}
        <h5>Paramètres par compagnie</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Compagnie</th>
                    <th>Logo</th>
                    {{-- <th>Couleur principale</th>
                    <th>Couleur secondaire</th> --}}
                    <th>Slogan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companyParametres as $p)
                    <tr>
                        <td>{{ $p->compagnie->nom ?? '-' }}</td>
                        <td><img src="{{ asset('storage/' . $p->logo) }}" width="60" /></td>
                        {{-- <td style="background: {{ $p->couleur_principale }}">{{ $p->couleur_principale }}</td>
                        <td style="background: {{ $p->couleur_secondaire }}">{{ $p->couleur_secondaire }}</td> --}}
                        <td>{{ $p->slogan }}</td>
                        <td><a href="{{ route('parametres.edit', $p) }}">Modifier</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Aucun paramètre de compagnie défini.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection
