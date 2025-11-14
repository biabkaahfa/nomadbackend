<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket Nomade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 20px;
        }
        .details {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: gray;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .section {
            margin-top: 30px;
        }
        .garres {
            margin-top: 10px;
        }
        .garre-item {
            margin-bottom: 8px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .localisation-section {
            margin: 20px 0;
            padding: 20px;
            background: #e7f3ff;
            border: 2px solid #0066cc;
            border-radius: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }
        .info-item {
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        .qr-container {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 style="color: #0066cc; margin-bottom: 10px;">🎫 TICKET DE VOYAGE</h1>
        <h2 style="margin: 10px 0; color: #333;">{{ $compagnie->name ?? 'Compagnie inconnue' }}</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>👤 Client :</strong><br>
                {{ $client }}
            </div>
            <div class="info-item">
                <strong>📅 Date du voyage :</strong><br>
                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- ✅ SECTION QR CODE -->
    @if(isset($qrCode) && !empty($qrCode))
    <div class="qr-container">
        <h3 style="margin: 0 0 15px 0; color: #0066cc;">📱 QR CODE DU TICKET</h3>
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="max-width: 200px; height: auto;">
        <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
            Présentez ce QR code lors de l'embarquement
        </p>
    </div>
    @endif

    <div class="details">
        <h3 style="color: #0066cc; margin-top: 0;">📋 INFORMATIONS DU VOYAGE</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>🎫 Numéro de Ticket :</strong><br>
                {{ $ticket_id }}
            </div>
            <div class="info-item">
                <strong>📍 Départ :</strong><br>
                {{ $depart }}
            </div>
            <div class="info-item">
                <strong>🏁 Arrivée :</strong><br>
                {{ $arrivee }}
            </div>
            <div class="info-item">
                <strong>🚌 Compagnie :</strong><br>
                {{ $compagnie->name ?? 'N/A' }}
            </div>
        </div>
    </div>

    <!-- ✅ SECTION GARE DE DÉPART SPÉCIFIQUE -->
    @if(isset($gare_depart) && $gare_depart)
    <div class="localisation-section">
        <h3 style="margin: 0 0 15px 0; color: #0066cc;">📍 LOCALISATION DE DÉPART RECOMMANDÉE</h3>

        <div class="info-grid">
            <div class="info-item">
                <strong>🏢 Gare :</strong><br>
                {{ $gare_depart['nom'] ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>🏙️ Ville :</strong><br>
                {{ $gare_depart['ville'] ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>📌 Coordonnées :</strong><br>
                {{ $gare_depart['localisation'] ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>🗺️ Lien Maps :</strong><br>
                <span style="color: #0066cc; font-size: 12px;">
                    https://maps.google.com?q={{ $gare_depart['localisation'] ?? '' }}
                </span>
            </div>
        </div>

        <div style="margin-top: 15px; padding: 10px; background: white; border-radius: 4px;">
            <p style="margin: 0; font-size: 12px; color: #666; text-align: center;">
                <em>Scannez le QR code ci-dessus ou utilisez le lien Google Maps pour localiser la gare</em>
            </p>
        </div>
    </div>
    @endif

    <!-- ✅ SECTION GARES DISPONIBLES (optionnelle) -->
    @if(isset($garres) && $garres->count())
        <div class="section">
            <h3 style="color: #0066cc;">🚏 AUTRES GARES DISPONIBLES</h3>
            <div class="garres">
                @foreach($garres as $garre)
                    <div class="garre-item">
                        <strong>{{ $garre->name ?? 'N/A' }}</strong> – {{ $garre->ville ?? 'N/A' }}<br>
                        <span style="font-size: 12px; color: #666;">
                            📍 {{ $garre->localisation ?? 'Localisation non disponible' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="footer">
        <p style="margin: 0 0 10px 0;">
            <strong>NOMAD</strong> – Votre plateforme de mobilité
        </p>
        <p style="margin: 0; font-size: 10px;">
            Ce ticket est valable pour un seul voyage. Conservez-le jusqu'à la fin de votre trajet.
        </p>
    </div>

</body>
</html>
