@extends('back.app')
@section('title',"Listes des personnalisations de carte")

@section('dashboard-header')
<h3 class="page-title mt-5">
    Listes des personnalisations de Carte
</h3>
@endsection

@section('dashboard-content')
<div class="card mt-3">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pays</th>
                    <th>Devise</th>
                    <th>Numéro</th>
                    <th>Couleur principale</th>
                    <th>Prix</th>
                    <th>Compagnie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personalisations as $perso)
                    <tr>
                        <td>{{ $perso->id }}</td>
                        <td>{{ $perso->pays }}</td>
                        <td>{{ $perso->devise }}</td>
                        <td>{{ $perso->numero }}</td>
                        <td>
                            <span style="background-color: {{ $perso->couleur_principale }};
                                          display:inline-block;
                                          width:25px;
                                          height:25px;
                                          border-radius:5px;
                                          border:1px solid #000;">
                            </span>
                            {{ $perso->couleur_principale }}
                        </td>
                        <td>{{ number_format($perso->prix, 2, ',', ' ') }} FCFA</td>
                        <td>{{ $perso->compagnie->name ?? '---' }}</td>
                        <td>
                            <a href="{{ route('personalisationCard.modifier', $perso) }}"
                               class="btn btn-warning btn-sm">
                                Modifier
                            </a>
                            <form action="{{ route('personalisationCard.destroy', $perso) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer cette personnalisation ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucune personnalisation trouvée</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
