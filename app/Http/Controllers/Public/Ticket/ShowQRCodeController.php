<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Ticket;

use Illuminate\View\View;
use App\Application\Bus\QueryBus;
use Endroid\QrCode\Builder\Builder;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Application\Reservations\Queries\GetReservationByNumberQuery;

/**
 * Contrôleur pour afficher le QR Code d'une réservation
 * RESPONSABILITÉ UNIQUE : Générer et afficher un QR Code
 */
class ShowQRCodeController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    /**
     * Génère et affiche le QR Code pour une réservation
     * GET /qr/{reservationNumber}
     */
    public function __invoke(string $reservationNumber): View
    {
        // Récupérer la réservation
        $query = new GetReservationByNumberQuery($reservationNumber);

        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            abort(404, 'Réservation introuvable');
        }

        $reservation = $result->getValue();

        // Données à encoder dans le QR Code
        $qrData = json_encode([
            'numero' => $reservation->numeroReservation,
            'id'     => $reservation->id->value,
            'date'   => $reservation->dateCreation->format('Y-m-d H:i:s'),
            'places' => $reservation->nombrePlaces,
        ]);

        // Générer le QR Code avec endroid/qr-code
        /** @phpstan-ignore-next-line staticMethod.notFound */
        $qrCode = Builder::create()
            ->writer(new SvgWriter)
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return view('public.ticket.qrcode', [
            'reservation' => $reservation,
            'qrCodeSvg'   => $qrCode->getString(),
        ]);
    }
}
