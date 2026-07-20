<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Ticket;

use Exception;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\SvgWriter;
use App\Domain\Cinema\Enums\ClassificationFilmEnum;
use App\Infrastructure\Database\Models\Cinema\Seance;
use App\Infrastructure\Database\Models\Reservations\Reservation;

/**
 * Contrôleur pour télécharger le billet PDF
 * FAT CONTROLLER : Récupère toutes les infos nécessaires pour le PDF
 */
class DownloadTicketController extends Controller
{
    /**
     * Génère et télécharge le billet PDF avec toutes les infos
     * GET /billet/{reservationNumber}
     */
    public function __invoke(string $reservationNumber): Response
    {
        // Récupérer la réservation avec Eloquent
        $reservation = Reservation::where('numero_reservation', $reservationNumber)->first();

        if (!$reservation) {
            abort(404, 'Réservation non trouvée');
        }

        // Récupérer la séance avec relations
        $seance = Seance::with(['film', 'salle.cinema'])->find($reservation->seance_db_id);

        if (!$seance) {
            abort(404, 'Séance non trouvée');
        }

        // Préparer les données formatées pour le PDF
        $data = [
            'numero_reservation' => $reservation->numero_reservation,
            'date_reservation'   => $reservation->created_at->format('d/m/Y à H:i'),

            // Film
            'film_titre'          => $seance->film->titre,
            'film_duree'          => $seance->film->getFormattedDurationAttribute(),
            'film_classification' => $seance->film->classification
                ? ClassificationFilmEnum::from($seance->film->classification)->label()
                : 'Non classé',
            'film_affiche'  => $seance->film->affiche_url,
            'film_synopsis' => $seance->film->synopsis,

            // Cinéma et salle
            'cinema_nom'     => $seance->salle->cinema->nom,
            'cinema_ville'   => $seance->salle->cinema->ville,
            'cinema_adresse' => $seance->salle->cinema->rue . ', ' .
                                $seance->salle->cinema->code_postal . ' ' .
                                $seance->salle->cinema->ville,
            'salle_nom' => $seance->salle->nom,

            // Séance
            'seance_date'    => $seance->date_heure_debut->format('d/m/Y'),
            'seance_heure'   => $seance->date_heure_debut->format('H:i'),
            'seance_version' => $seance->version,

            // Prix et places
            'prix_total'    => number_format($reservation->prix_total_ttc_centimes / 100, 2, ',', ' ') . ' €',
            'nombre_places' => $reservation->nombre_places,
            'places_text'   => $reservation->nombre_places . ' ' . ($reservation->nombre_places > 1 ? 'places' : 'place'),

            // Statut
            'statut' => $this->getStatutLabel($reservation->statut),
        ];

        // Sièges si disponibles
        $seats = [];
        if (is_array($reservation->details_places) && isset($reservation->details_places['places'])) {
            foreach ($reservation->details_places['places'] as $place) {
                if (isset($place['rangee']) && isset($place['numero'])) {
                    $seats[] = $place['rangee'] . $place['numero'];
                }
            }
        }
        $data['sieges'] = !empty($seats) ? implode(', ', $seats) : null;

        // Générer le QR code en base64
        $qrData = json_encode([
            'reservation' => $reservation->numero_reservation,
            'seance'      => $seance->uuid,
            'date'        => $seance->date_heure_debut->format('Y-m-d H:i'),
            'places'      => $reservation->nombre_places,
        ]);

        // Créer le QR code avec SVG (pas besoin de GD)
        try {
            // Données simplifiées pour le QR code
            $qrSimpleData = $reservation->numero_reservation;

            $qrCode = new QrCode(
                data: $qrSimpleData,
                size: 120,
                margin: 5
            );

            $writer = new SvgWriter;
            $result = $writer->write($qrCode);

            // Obtenir le SVG et le convertir en data URI
            $svgContent      = $result->getString();
            $data['qr_code'] = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

        } catch (Exception $e) {
            // Si erreur, pas de QR code
            $data['qr_code']  = null;
            $data['qr_error'] = $e->getMessage();
        }

        // Générer le PDF
        $pdf = Pdf::loadView('public.ticket.pdf', compact('data'));

        // Configuration du PDF avec autorisation des images
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled'      => true,    // Autorise les images externes
            'isPhpEnabled'         => false,      // Désactive PHP pour sécurité
            'isHtml5ParserEnabled' => true, // Parse HTML5
        ]);

        // Télécharger le PDF
        return $pdf->download("billet-{$reservationNumber}.pdf");
    }

    /**
     * Convertit le statut en label lisible
     */
    private function getStatutLabel(string $statut): string
    {
        return match ($statut) {
            'EN_ATTENTE_PAIEMENT' => 'En attente de paiement',
            'CONFIRMEE'           => 'Confirmée',
            'PAYEE'               => 'Payée',
            'UTILISEE'            => 'Utilisée',
            'EXPIREE'             => 'Expirée',
            'ANNULEE'             => 'Annulée',
            default               => $statut
        };
    }
}
