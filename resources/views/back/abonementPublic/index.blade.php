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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucun abonnement trouvé.</td>
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
