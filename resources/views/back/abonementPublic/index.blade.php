@extends('back.app')

@section('title', 'Liste des Abonnements Publics')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <div class="mt-5">
            <h4 class="card-title float-left mt-2">Abonnements Publics</h4>
            <a href="{{ route('abonementPublic.create') }}" class="btn btn-primary float-right viewbutton">
                Ajouter un abonnement
            </a>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-sm-12">

        {{-- ✅ Messages de succès ou erreur --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-table">
            <div class="card-body booking_card">
                <div class="table-responsive">
                    <table class="datatable table table-striped table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Nom & Prénom</th>
                                <th>Profession</th>
                                <th>Établissement</th>
                                <th>Compagnie</th>
                                <th>Date Début</th>
                                <th>Date Fin</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeSubscriptions as $abonnement)
                                <tr>
                                    <td>{{ $abonnement->id }}</td>
                                    <td>
                                        @if ($abonnement->photo)
                                            <img src="{{ asset('storage/' . $abonnement->photo) }}"
                                                 alt="Photo de {{ $abonnement->nom }}"
                                                 class="rounded-circle"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <!-- Icône de personne par défaut si aucune photo n'est disponible -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-circle text-muted" viewBox="0 0 16 16">
                                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                            </svg>
                                        @endif
                                    </td>
                                    <td>{{ $abonnement->nom }} {{ $abonnement->prenom }}</td>
                                    <td>{{ $abonnement->profession }}</td>
                                    <td>{{ $abonnement->etablissement }}</td>
                                    <td>{{ $abonnement->compagnie->name ?? 'Non associée' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($abonnement->dateDebut)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($abonnement->dateFin)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($abonnement->statut === 'actif')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('abonementPublic.edit', $abonnement) }}" class="btn btn-sm btn-warning">Modifier</a>
                                        <form action="{{ route('abonementPublic.destroy', $abonnement) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Supprimer cet abonnement ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                        </form>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#carteModal{{ $abonnement->id }}">
                                            Voir la carte
                                        </button>
                                    </td>
                                </tr>

                                {{-- ✅ Modal pour afficher la carte --}}
                                <div class="modal fade" id="carteModal{{ $abonnement->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Carte d'abonnement</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">

                                                {{-- ✅ Carte design --}}
                                                <div class="carte-abonnement"
                                                     style="background: {{ $abonnement->compagnie->personalisationCard->couleur_principale ?? '#2c3e50' }};
                                                             color: white; border-radius: 15px; padding: 20px; position: relative; display: flex; align-items: center;">

                                                    {{-- Logo compagnie --}}
                                                    @if($abonnement->compagnie && $abonnement->compagnie->logo)
                                                        <img src="{{ asset('storage/'.$abonnement->compagnie->logo) }}"
                                                             alt="Logo"
                                                             style="height: 30px; position: absolute; top: 20px; left: 20px;">
                                                    @endif

                                                    {{-- Photo de l'abonné --}}
                                                    <div style="flex-shrink: 0;  position: absolute; top: 70px; left: 20px; ">
                                                        @if ($abonnement->photo)
                                                            <img src="{{ asset('storage/' . $abonnement->photo) }}"
                                                                 alt="Photo de {{ $abonnement->nom }}"
                                                                 class="rounded-circle"
                                                                 style="width: 100px; height: 100px; object-fit: cover; border: 2px solid white;">
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-person-circle text-muted" viewBox="0 0 16 16" style="border: 2px solid white; border-radius: 50%;">
                                                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    {{-- Infos utilisateur --}}
                                                    <div>
                                                        <h4>{{ $abonnement->nom }} {{ $abonnement->prenom }}</h4>
                                                        <p><strong>Profession:</strong> {{ $abonnement->profession }}</p>
                                                        <p><strong>Établissement:</strong> {{ $abonnement->etablissement }}</p>
                                                    </div>

                                                    {{-- Infos personnalisation et QR Code --}}
                                                    <div style="position: absolute; top: 20px; right: 20px; text-align: right;">
                                                        <p><strong>Pays:</strong> {{ optional($abonnement->compagnie->personalisationCard)->pays ?? 'N/A' }}</p>
                                                        <p><strong>Devise:</strong> {{ optional($abonnement->compagnie->personalisationCard)->devise ?? 'N/A' }}</p>
                                                        <p><strong>N°:</strong> {{ optional($abonnement->compagnie->personalisationCard)->numero ?? 'N/A' }}</p>
                                                    </div>
                                                    <div style="position: absolute; bottom: 20px; right: 20px;">
                                                        {!! QrCode::size(100)->generate("ID: {$abonnement->id} | Nom: {$abonnement->nom} {$abonnement->prenom} | Début: {$abonnement->dateDebut} | Fin: {$abonnement->dateFin}") !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Aucun abonnement trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






{{--
@extends('back.app')

@section('title', 'Liste des Abonnements Publics')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Abonnements Publics</h4>
                <a href="{{ route('abonementPublic.create') }}" class="btn btn-primary float-right viewbutton">
                    Ajouter un abonnement
                </a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-sm-12">

            {{-- ✅ Messages de succès ou erreur
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="container mx-auto p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($activeSubscriptions as $abonnement)
                        {{-- Récupérer la carte de personnalisation liée
                        @php
                            $persoCard = $abonnement->personalisationCard;
                            // Définir une couleur par défaut si aucune personnalisation n'est trouvée
                            $cardColor = $persoCard ? $persoCard->couleur_principale : '#f3f4f6';
                        @endphp

                        {{-- Afficher la carte d'abonnement --
                        <div class="bg-white rounded-lg shadow-xl overflow-hidden transform transition-transform duration-300 hover:scale-105">
                            <div class="h-28 text-white flex flex-col justify-between p-4" style="background-color: {{ $cardColor }};">
                                <div class="flex justify-between items-center">
                                    <h5 class="text-xl font-bold">{{ $abonnement->compagnie->name ?? 'Compagnie' }}</h5>
                                    <div class="text-xs font-semibold">
                                        {{-- Afficher le numéro personnalisé si disponible --
                                        <p>{{ $persoCard ? $persoCard->numero : 'N°' }}</p>
                                    </div>
                                </div>
                                <div class="text-sm">
                                    <p>{{ $persoCard ? $persoCard->pays : 'Pays' }}</p>
                                    <p>{{ $persoCard ? $persoCard->devise : 'Devise' }}</p>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="flex items-center space-x-4 mb-4">
                                    @if ($abonnement->Photo)
                                        <img src="{{ Storage::url($abonnement->Photo) }}" alt="Photo de profil" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-3xl font-semibold">
                                            <span>
                                                {{ substr($abonnement->prenom, 0, 1) }}
                                                {{ substr($abonnement->nom, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $abonnement->nom }} {{ $abonnement->prenom }}</h3>
                                        <p class="text-sm text-gray-600">{{ $abonnement->profession }}</p>
                                    </div>
                                </div>

                                <ul class="text-sm text-gray-700">
                                    <li class="mb-2"><strong>Établissement:</strong> {{ $abonnement->etablissement }}</li>
                                    <li class="mb-2"><strong>Début:</strong> {{ \Carbon\Carbon::parse($abonnement->dateDebut)->format('d/m/Y') }}</li>
                                    <li class="mb-2"><strong>Fin:</strong> {{ \Carbon\Carbon::parse($abonnement->dateFin)->format('d/m/Y') }}</li>
                                    <li class="mb-2">
                                        <strong>Statut:</strong>
                                        @if($abonnement->statut === 'actif')
                                            <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full ml-1">Actif</span>
                                        @else
                                            <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full ml-1">Inactif</span>
                                        @endif
                                    </li>
                                </ul>

                                <div class="mt-4 flex justify-end space-x-2">
                                    <a href="{{ route('abonementPublic.edit', $abonnement) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-600 transition-colors">Modifier</a>
                                    <form action="{{ route('abonementPublic.destroy', $abonnement) }}" method="POST" onsubmit="return confirm('Supprimer cet abonnement ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-500 text-lg">
                            Aucun abonnement trouvé.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection --}}
