<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billet Cinéphoria - {{ $reservation['numeroReservation'] }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
            line-height: 1.4;
        }

        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .header {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: #000;
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                white 10px,
                white 20px
            );
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .reservation-number {
            background: rgba(0,0,0,0.1);
            padding: 8px 16px;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
        }

        .content {
            padding: 30px;
        }

        .film-info {
            margin-bottom: 25px;
        }

        .film-title {
            font-size: 24px;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 8px;
        }

        .film-details {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .seance-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
        }

        .info-block {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .qr-section {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .qr-pattern {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1px;
            padding: 8px;
            width: 100%;
            height: 100%;
        }

        .qr-pixel {
            background: #000;
            aspect-ratio: 1;
        }

        .qr-pixel.white {
            background: transparent;
        }

        .instructions {
            margin-top: 25px;
            padding: 20px;
            background: #e8f4fd;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }

        .instructions h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
            font-size: 16px;
        }

        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 5px;
            font-size: 14px;
            color: #666;
        }

        .footer {
            margin-top: 30px;
            padding: 20px;
            background: #f1f1f1;
            border-radius: 8px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .footer .contact {
            margin-top: 10px;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #d4af37;
        }

        .divider {
            border: none;
            border-top: 2px dashed #d0d0d0;
            margin: 25px 0;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .ticket {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header avec logo -->
        <div class="header">
            <div class="logo">Cinéphoria</div>
            <div class="subtitle">Billet Électronique</div>
            <div class="reservation-number">{{ $reservation['numeroReservation'] }}</div>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <!-- Informations du film -->
            <div class="film-info">
                <div class="film-title">{{ $reservation['film'] }}</div>
                <div class="film-details">
                    {{ $reservation['genre'] ?? 'Film' }} • {{ $reservation['duree'] ?? '120' }} minutes
                    @if(isset($reservation['classification']))
                        • {{ $reservation['classification'] }}
                    @endif
                </div>
            </div>

            <!-- Informations de la séance -->
            <div class="seance-info">
                <div class="info-block">
                    <div class="info-label">Date & Heure</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($reservation['dateHeure'])->locale('fr')->isoFormat('dddd D MMMM YYYY') }}<br>
                        <span style="font-size: 20px; color: #d4af37;">{{ \Carbon\Carbon::parse($reservation['dateHeure'])->format('H:i') }}</span>
                    </div>
                </div>

                <div class="info-block">
                    <div class="info-label">Cinéma & Salle</div>
                    <div class="info-value">
                        {{ $reservation['cinema'] ?? 'Cinéphoria' }}<br>
                        {{ $reservation['salle'] }}
                    </div>
                </div>

                <div class="info-block">
                    <div class="info-label">Places</div>
                    <div class="info-value">
                        {{ $reservation['nbPlaces'] }} place{{ $reservation['nbPlaces'] > 1 ? 's' : '' }}<br>
                        @if(isset($reservation['places']) && is_array($reservation['places']))
                            {{ implode(', ', $reservation['places']) }}
                        @endif
                    </div>
                </div>

                <div class="info-block">
                    <div class="info-label">Total payé</div>
                    <div class="info-value price">{{ $reservation['total'] }}€</div>
                </div>
            </div>

            <hr class="divider">

            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-code">
                    <div class="qr-pattern">
                        @for($i = 0; $i < 144; $i++)
                            <div class="qr-pixel {{ rand(0, 1) ? '' : 'white' }}"></div>
                        @endfor
                    </div>
                </div>
                <div style="font-family: 'Courier New', monospace; font-weight: bold; color: #666;">
                    {{ $reservation['numeroReservation'] }}
                </div>
                <div style="font-size: 12px; color: #999; margin-top: 5px;">
                    Présentez ce QR code à l'entrée du cinéma
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h4>Instructions importantes :</h4>
                <ul>
                    <li>Arrivez au moins 15 minutes avant le début de la séance</li>
                    <li>Présentez ce billet (imprimé ou sur mobile) à l'accueil</li>
                    <li>Gardez votre billet pendant toute la séance</li>
                    <li>Les retards ne sont pas tolérés après le début du film</li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div><strong>Cinéphoria</strong> - Votre cinéma de référence</div>
                <div class="contact">
                    📍 123 Avenue du Cinéma, 75001 Paris<br>
                    📞 01 23 45 67 89 • ✉️ contact@cinephoria.fr<br>
                    🌐 www.cinephoria.fr
                </div>
                <div style="margin-top: 15px; font-size: 10px; color: #999;">
                    Billet généré le {{ now()->locale('fr')->isoFormat('DD/MM/YYYY [à] HH[h]mm') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>