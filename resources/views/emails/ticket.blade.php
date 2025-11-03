<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket NOMAD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 1rem;
        }
        h1 {
            color: #007BFF;
        }
        ul {
            padding-left: 1.2rem;
        }
        a {
            color: #0069d9;
            text-decoration: none;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .localisation-section {
            margin: 15px 0;
            padding: 15px;
            background: #f0f8ff;
            border: 1px solid #0066cc;
            border-radius: 8px;
        }
        .map-link {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Bonjour {{ $client }}</h1>

    <p>Merci pour votre achat. Vous trouverez votre ticket en pièce jointe.</p>

    <p>
        <strong>🚌 Compagnie :</strong> {{ $compagnie->name ?? 'Non précisée' }}<br>
        <strong>📅 Date de départ :</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}<br>
        <strong>🎫 Numéro de ticket :</strong> {{ $ticket_id ?? 'N/A' }}<br>
        <strong>📍 Départ :</strong> {{ $depart ?? 'N/A' }}<br>
        <strong>🏁 Arrivée :</strong> {{ $arrivee ?? 'N/A' }}
    </p>

    {{-- <!-- ✅ SECTION QR CODE -->
    @if(isset($qrCode) && !empty($qrCode))
    <div class="qr-code">
        <h3>📱 QR Code de votre ticket</h3>
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code du ticket" style="max-width: 200px; height: auto;">
        <p><em>Présentez ce QR code lors de l'embarquement</em></p>
    </div>
    @endif --}}

    <!-- ✅ SECTION GARE DE DÉPART SPÉCIFIQUE -->
    @if(isset($gare_depart) && $gare_depart)
    <div class="localisation-section">
        <h3 style="margin: 0 0 10px 0; color: #0066cc;">📍 Localisation de départ recommandée</h3>

        <p><strong>🏢 Gare :</strong> {{ $gare_depart['name'] ?? 'N/A' }}</p>
        <p><strong>🏙️ Ville :</strong> {{ $gare_depart['ville'] ?? 'N/A' }}</p>
        <p><strong>📌 Coordonnées :</strong>
            @if(isset($gare_depart['localisation']) && !empty($gare_depart['localisation']))
            <a href="https://www.google.com/maps?q={{ $gare_depart['localisation'] }}"
               class="map-link"
               target="_blank">
               🗺️ {{ $gare_depart['localisation'] }}
            </a>
            @else
            🗺️ Localisation non disponible
            @endif
        </p>

        <div style="margin-top: 10px; font-size: 12px; color: #666;">
            <em>Cliquez sur les coordonnées pour ouvrir dans Google Maps</em>
        </div>
    </div>
    @endif

    <!-- ✅ SECTION GARES DISPONIBLES (optionnelle) -->
    @if(isset($garres) && count($garres))
        <h3>🚏 Autres gares disponibles pour ce trajet :</h3>
        <ul>
            @foreach($garres as $garre)
                <li>
                    <strong>{{ $garre->name ?? 'N/A' }}</strong> – {{ $garre->ville ?? 'N/A' }}<br>
                    @if($garre->localisation)
                    <a href="https://www.google.com/maps?q={{ $garre->localisation }}" target="_blank">
                        📍 Voir sur Google Maps
                    </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <p>📎 Le ticket détaillé est en pièce jointe au format PDF.</p>

    <p style="margin-top: 2rem;">Merci d'avoir utilisé la plateforme <strong>NOMAD</strong> – Votre plateforme de mobilité.</p>
</body>
</html>
