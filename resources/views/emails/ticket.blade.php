{{-- Ancienne version en composant Blade Mail (optionnelle) --}}
{{--
@component('mail::message')
# Bonjour/Bonsoir chers {{ $client }}

Voici votre ticket pour votre voyage avec **{{ $compagnie->name }}**.

Vous trouverez le fichier PDF en pièce jointe.

Merci d’avoir utilisé la plateforme **NOMAD**.
@endcomponent
--}}

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
    </style>
</head>
<body>
    <h1>Bonjour {{ $client }}</h1>

    <p>Merci pour votre achat. Vous trouverez votre ticket en pièce jointe.</p>

    <p>
        <strong>🚌 Compagnie :</strong> {{ $compagnie->name ?? 'Non précisée' }}<br>
        <strong>📅 Date de départ :</strong> {{ $date }}
    </p>

    @if($garres && count($garres))
        <h3>🚏 Gares disponibles pour embarquer :</h3>
        <ul>
            @foreach($garres as $garre)
                <li>
                    <strong>{{ $garre->name}}</strong> – {{ $garre->ville }} {{ $garre->localisation }}<br>
                    {{-- 📍 <a href="https://www.google.com/maps?q={{ $garre->latitude }},{{ $garre->longitude }}" target="_blank">
                        Voir sur Google Maps
                    </a> --}}
                </li>
            @endforeach
        </ul>
    @endif

    <p>📎 Le ticket est en pièce jointe au format PDF.</p>

    <p style="margin-top: 2rem;">Merci d’avoir utilisé la plateforme <strong>NOMAD</strong>.</p>
</body>
</html>
