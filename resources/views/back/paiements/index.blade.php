@extends('back.app')
@section('title', 'Tableau de bord des paiements')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <div class="mt-5 d-flex justify-content-between align-items-center">
            <h4 class="card-title">Tableau de bord des paiements</h4>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="mb-4">
    <form method="GET" class="form-inline d-flex gap-2 flex-wrap" style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Référence ou Téléphone" class="form-control">

        <select name="periode" class="form-control" style="font-size: 1.3rem; padding: 1rem; height:auto;">
            <option value="jour" {{ request('periode') == 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
            <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
            <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>Ce mois</option>
            <option value="annee" {{ request('periode') == 'annee' ? 'selected' : '' }}>Cette année</option>
        </select>

        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-header">Évolution des paiements</div>
    <div class="card-body">
        <canvas id="paiementChart"></canvas>
    </div>
</div>

<h5>Montants totaux par type :</h5>
<ul>
    <li>Orange Money : {{ array_sum($chartData['OM'] ?? []) }} F</li>
    <li>Moov Money : {{ array_sum($chartData['MOOV'] ?? []) }} F</li>
    <li>Espèces : {{ array_sum($chartData['ESPECE'] ?? []) }} F</li>
    <li>Carte : {{ array_sum($chartData['CARTE'] ?? []) }} F</li>
</ul>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Montant</th>
                    <th>Moyen</th>
                    <th>Statut</th>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiements as $p)
                <tr>
                    <td>
                        @if($p->ticket)
                            @if(!empty($p->ticket->name))
                                {{ $p->ticket->name }}
                            @elseif($p->ticket->typeAchat === 'en_ligne' && $p->ticket->utilisateur)
                                {{ $p->ticket->utilisateur->name }}
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $p->montant }} F</td>
                    <td>{{ $p->moyenPaiement }}</td>
                    <td>{{ $p->statut }}</td>
                    <td>{{ $p->referenceTransaction }}</td>
                    <td>{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('paiements.edit', $p) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('paiements.destroy', $p) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce paiement ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('paiementChart');

const chartData = @json($chartData);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Orange Money',
                data: chartData.OM,
                backgroundColor: 'orange'
            },
            {
                label: 'Moov Money',
                data: chartData.MOOV,
                backgroundColor: 'blue'
            },
            {
                label: 'Espèces',
                data: chartData.ESPECE,
                backgroundColor: 'green'
            },
            {
                label: 'Carte',
                data: chartData.CARTE,
                backgroundColor: 'purple'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            title: {
                display: true,
                text: 'Évolution des paiements par type (Barres)'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => value + ' F'
                }
            }
        }
    }
});
</script>
@endsection
