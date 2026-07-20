<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billet - {{ $data['numero_reservation'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #ffffff;
            color: #000000;
            padding: 20px;
        }

        .ticket {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #d4af37;
            border-radius: 15px;
            overflow: hidden;
        }

        .ticket-header {
            background: linear-gradient(90deg, #d4af37 0%, #f4e55c 100%);
            color: #000000;
            padding: 20px;
            text-align: center;
        }

        .cinema-logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .ticket-type {
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-body {
            padding: 30px;
        }

        .film-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .film-poster {
            display: table-cell;
            width: 120px;
            vertical-align: top;
            padding-right: 20px;
        }

        .film-poster img {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #d4af37;
        }

        .film-info {
            display: table-cell;
            vertical-align: top;
        }

        .film-title {
            color: #d4af37;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .film-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }

        .info-col:first-child {
            padding-left: 0;
        }

        .info-col:last-child {
            padding-right: 0;
        }

        .detail-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        .detail-value {
            text-align: right;
            color: #000;
            font-weight: 500;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
        }

        .qr-code {
            margin: 0 auto 15px;
        }

        .qr-code img {
            width: 150px;
            height: 150px;
        }

        .reservation-number {
            font-size: 20px;
            font-weight: bold;
            color: #d4af37;
            text-align: center;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .price-section {
            background: #333;
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .price-total {
            font-size: 24px;
            font-weight: bold;
            color: #d4af37;
            text-align: center;
            margin-top: 10px;
        }

        .ticket-footer {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 2px dashed #d4af37;
            margin-top: 30px;
        }

        .footer-text {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #28a745;
            color: #fff;
        }

        .seats-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="ticket-header">
            <div class="cinema-logo">CINÉPHORIA</div>
            <div class="ticket-type">E-Billet Cinéma</div>
        </div>

        <!-- Corps du ticket -->
        <div class="ticket-body">
            <!-- Section Film avec affiche -->
            <div class="film-section">
                @if($data['film_affiche'])
                <div class="film-poster">
                    <img src="{{ $data['film_affiche'] }}" alt="{{ $data['film_titre'] }}">
                </div>
                @endif
                <div class="film-info">
                    <h2 class="film-title">{{ $data['film_titre'] }}</h2>
                    <div class="film-meta">
                        <strong>Classification:</strong> {{ $data['film_classification'] }} |
                        <strong>Durée:</strong> {{ $data['film_duree'] }} |
                        <strong>Version:</strong> {{ $data['seance_version'] }}
                    </div>
                </div>
            </div>

            <!-- Informations en 2 colonnes -->
            <div class="info-grid">
                <div class="info-col">
                    <div class="detail-box">
                        <h3 style="color: #d4af37; margin-bottom: 10px;">LIEU</h3>
                        <div class="detail-row">
                            <span class="detail-label">Cinéma :</span>
                            <span class="detail-value">{{ $data['cinema_nom'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ville :</span>
                            <span class="detail-value">{{ $data['cinema_ville'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Salle :</span>
                            <span class="detail-value">{{ $data['salle_nom'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="info-col">
                    <div class="detail-box">
                        <h3 style="color: #d4af37; margin-bottom: 10px;">SEANCE</h3>
                        <div class="detail-row">
                            <span class="detail-label">Date :</span>
                            <span class="detail-value">{{ $data['seance_date'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Heure :</span>
                            <span class="detail-value">{{ $data['seance_heure'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Places :</span>
                            <span class="detail-value">{{ $data['places_text'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sièges si disponibles -->
            @if($data['sieges'])
            <div class="seats-info">
                <strong>Sièges réservés :</strong> {{ $data['sieges'] }}
            </div>
            @endif

            <!-- QR Code et numéro de réservation -->
            <div class="qr-section">
                <div class="reservation-number">{{ $data['numero_reservation'] }}</div>

                @if($data['qr_code'])
                <div class="qr-code">
                    <img src="{{ $data['qr_code'] }}" alt="QR Code" style="width: 120px; height: 120px;">
                </div>
                @else
                <div style="width: 120px; height: 120px; background: #f0f0f0; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc; border-radius: 8px;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 5px;">QR CODE</div>
                        <div style="font-size: 10px; color: #666;">{{ $data['numero_reservation'] }}</div>
                        @if(isset($data['qr_error']))
                        <div style="font-size: 8px; color: #f00; margin-top: 5px;">Erreur: {{ $data['qr_error'] }}</div>
                        @endif
                    </div>
                </div>
                @endif

                <div style="margin-top: 10px;">
                    <span class="status-badge">{{ $data['statut'] }}</span>
                </div>
            </div>

            <!-- Prix -->
            <div class="price-section">
                <div style="text-align: center; color: #fff;">
                    <div style="margin-bottom: 5px;">Montant total</div>
                    <div class="price-total">{{ $data['prix_total'] }}</div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div style="margin-top: 20px; padding: 15px; background: #e8f4fd; border-left: 4px solid #007bff; font-size: 13px;">
                <strong>Informations importantes :</strong><br>
                • Réservation effectuée le {{ $data['date_reservation'] }}<br>
                • Présentez ce billet sur votre mobile ou imprimé<br>
                • Arrivez 15 minutes avant le début de la séance<br>
                • Le placement est garanti jusqu'à 5 minutes avant le début du film
            </div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            <div class="footer-text">
                <strong>Cinéphoria - L'expérience cinéma premium</strong><br>
                Ce billet est personnel et non cessible. Il est valable uniquement pour la séance indiquée.<br>
                En cas de problème, contactez notre service client : contact@cinephoria.fr
            </div>
        </div>
    </div>
</body>
</html>