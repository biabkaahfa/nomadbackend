<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe modifié - Movyx</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0066CC 0%, #004499 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .success-icon {
            text-align: center;
            margin-bottom: 30px;
        }
        .success-icon .circle {
            width: 80px;
            height: 80px;
            background: #00C851;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .success-icon .checkmark {
            color: white;
            font-size: 40px;
            font-weight: bold;
        }
        .title {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #0066CC;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .warning-text {
            color: #856404;
            font-size: 14px;
            margin: 0;
        }
        .support {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .support-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .support-email {
            color: #0066CC;
            font-weight: 600;
            text-decoration: none;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer-text {
            color: #666;
            font-size: 12px;
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background: #0066CC;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">MOVYX</div>
            <div class="tagline">Votre partenaire de transport intelligent</div>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <!-- Icône de succès -->
            <div class="success-icon">
                <div class="circle">
                    <div class="checkmark">✓</div>
                </div>
            </div>

            <!-- Titre -->
            <h1 class="title">Mot de passe modifié avec succès</h1>

            <!-- Message -->
            <div class="message">
                Votre mot de passe a été modifié avec succès. Si vous n'êtes pas à l'origine de cette modification, veuillez contacter immédiatement notre support.
            </div>

            <!-- Informations de changement -->
            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">Nom :</span>
                    <span class="info-value">{{ $name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de modification :</span>
                    <span class="info-value">{{ $changeDate }}</span>
                </div>
            </div>

            <!-- Avertissement de sécurité -->
            <div class="warning">
                <p class="warning-text">
                    ⚠️ Pour votre sécurité, ne partagez jamais votre mot de passe avec qui que ce soit.
                </p>
            </div>

            <!-- Bouton de connexion -->
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">
                    Se connecter à mon compte
                </a>
            </div>

            <!-- Support -->
            <div class="support">
                <p class="support-text">
                    Vous avez des questions ? Contactez notre support :
                </p>
                <a href="mailto:support@movyx.com" class="support-email">
                    support@movyx.com
                </a>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p class="footer-text">© {{ date('Y') }} Movyx. Tous droits réservés.</p>
            <p class="footer-text">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
