<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0066CC; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .token { background: #e3f2fd; padding: 15px; margin: 15px 0; font-family: monospace; font-size: 18px; text-align: center; }
        .button { display: inline-block; background: #0066CC; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Movyx</h1>

            <h2>Réinitialisation de mot de passe</h2>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $name }}</strong>,</p>

            <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>

            <p>Voici votre code de réinitialisation :</p>

            <div class="token">
                <strong>{{ $token }}</strong>
            </div>

            <p>Vous pouvez également cliquer sur le lien ci-dessous :</p>

            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Réinitialiser mon mot de passe</a>
            </p>

            <p><strong>Attention :</strong> Ce code expirera dans {{ $expiresIn }}.</p>

            <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Movyx. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
