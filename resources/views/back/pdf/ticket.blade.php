<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket Nomade</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: gray; }
        .section { margin-top: 30px; }
        .garres { margin-top: 10px; }
        .garre-item { margin-bottom: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <h2> Ticket de voyage - {{ $compagnie->name ?? 'Compagnie inconnue' }}</h2>
        <p><strong>Client :</strong> {{ $client }}</p>
        <p><strong>Date du voyage :</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code" style="margin-top: 20px;">
    </div>

    <div class="details">
        <p><strong> Numéro de Ticket :</strong> {{ $ticket_id }}</p>
        <p><strong> Départ :</strong> {{ $depart }}</p>
        <p><strong> Arrivée :</strong> {{ $arrivee }}</p>
    </div>

    @if(isset($garres) && $garres->count())
        <div class="section">
            <h4>Gares desservant ce trajet :</h4>
            <ul class="garres">
                @foreach($garres as $garre)
                    <li class="garre-item">
                        <strong>{{ $garre->name }}</strong> – {{ $garre->ville }}<br>
                         Coordonnées : {{ $garre->localisation }}, 
                        <br>
                        {{-- 🔗 <a href="https://www.google.com/maps?q={{ $garre->latitude }},{{ $garre->longitude }}" target="_blank"> --}}
                            Voir sur Google Maps
                        {{-- </a> --}}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        Merci d’avoir choisi <strong>NOMAD</strong> – Votre plateforme de mobilité
    </div>

</body>
</html>
