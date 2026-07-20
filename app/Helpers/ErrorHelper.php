<?php

declare(strict_types=1);

namespace App\Helpers;

use Throwable;

final class ErrorHelper
{
    /**
     * Génère un message d'erreur contextuel selon l'environnement
     *
     * @return array{title: string, message: string, icon: string, confirmButtonText: string, redirectUrl?: string}
     */
    public static function getErrorMessage(int $statusCode, ?Throwable $exception = null): array
    {
        $isProduction = app()->environment('production');

        return match ($statusCode) {
            404 => [
                'title'   => 'Page non trouvée',
                'message' => $isProduction
                    ? 'La page que vous recherchez n\'existe pas ou a été supprimée.'
                    : 'Page non trouvée : ' . ($exception?->getMessage() ?? 'Route inconnue'),
                'icon'              => 'error',
                'confirmButtonText' => 'Retour à l\'accueil',
                'redirectUrl'       => route('home'),
            ],

            403 => [
                'title'   => 'Accès interdit',
                'message' => $isProduction
                    ? 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.'
                    : 'Accès refusé : ' . ($exception?->getMessage() ?? 'Permissions insuffisantes'),
                'icon'              => 'warning',
                'confirmButtonText' => 'Page précédente',
                'redirectUrl'       => 'javascript:history.back()',
            ],

            500 => [
                'title'   => 'Erreur serveur',
                'message' => $isProduction
                    ? 'Une erreur technique s\'est produite. Nos équipes ont été averties.'
                    : 'Erreur serveur : ' . ($exception?->getMessage() ?? 'Erreur interne'),
                'icon'              => 'error',
                'confirmButtonText' => 'Retour à l\'accueil',
                'redirectUrl'       => route('home'),
            ],

            419 => [
                'title'   => 'Session expirée',
                'message' => $isProduction
                    ? 'Votre session a expiré. Veuillez actualiser la page.'
                    : 'Token CSRF expiré : ' . ($exception?->getMessage() ?? 'Session invalide'),
                'icon'              => 'warning',
                'confirmButtonText' => 'Actualiser',
                'redirectUrl'       => 'javascript:location.reload()',
            ],

            default => [
                'title'   => 'Erreur',
                'message' => $isProduction
                    ? 'Une erreur inattendue s\'est produite.'
                    : 'Erreur ' . $statusCode . ' : ' . ($exception?->getMessage() ?? 'Erreur inconnue'),
                'icon'              => 'error',
                'confirmButtonText' => 'Retour',
                'redirectUrl'       => 'javascript:history.back()',
            ],
        };
    }

    /**
     * Détermine si une erreur doit être gérée avec SweetAlert2
     */
    public static function shouldUseSweetAlert(int $statusCode): bool
    {
        return in_array($statusCode, [403, 404, 419, 500, 503]);
    }

    /**
     * Génère le JavaScript SweetAlert2 pour une erreur
     *
     * @param array{title: string, message: string, icon: string, confirmButtonText: string, redirectUrl?: string} $errorData
     */
    public static function generateSweetAlertScript(array $errorData): string
    {
        $config = [
            'title'             => $errorData['title'],
            'html'              => $errorData['message'],
            'icon'              => $errorData['icon'],
            'confirmButtonText' => $errorData['confirmButtonText'],
            'allowOutsideClick' => false,
            'allowEscapeKey'    => false,
        ];

        $configJson  = json_encode($config, JSON_UNESCAPED_UNICODE);
        $redirectUrl = $errorData['redirectUrl'];

        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire($configJson).then((result) => {
                if (result.isConfirmed) {
                    if ('$redirectUrl'.startsWith('javascript:')) {
                        eval('$redirectUrl'.substring(11));
                    } else {
                        window.location.href = '$redirectUrl';
                    }
                }
            });
        });
        </script>";
    }
}
