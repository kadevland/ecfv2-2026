<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use Throwable;
use App\Helpers\ErrorHelper;
use Illuminate\Http\RedirectResponse;

trait HandlesErrorsWithSweetAlert
{
    /**
     * Affiche une erreur avec SweetAlert2 et redirige vers une page
     */
    protected function handleErrorWithSweetAlert(
        int $statusCode,
        ?Throwable $exception = null,
        ?string $customMessage = null,
        ?string $redirectUrl = null
    ): RedirectResponse {
        $errorData = ErrorHelper::getErrorMessage($statusCode, $exception);

        // Override avec message personnalisé si fourni
        if ($customMessage) {
            $errorData['message'] = $customMessage;
        }

        // Override avec URL de redirection personnalisée si fournie
        if ($redirectUrl) {
            $errorData['redirectUrl'] = $redirectUrl;
        }

        // Utiliser SweetAlert2 via flash
        /** @phpstan-ignore-next-line method.notFound */
        flash()->use('sweetalert')
            ->title($errorData['title'])
            ->html($errorData['message'])
            ->icon($errorData['icon'])
            ->confirmButtonText($errorData['confirmButtonText'])
            ->allowOutsideClick(false)
            ->allowEscapeKey(false);

        // Rediriger vers l'URL spécifiée ou la page précédente
        if ($redirectUrl && !str_starts_with($redirectUrl, 'javascript:')) {
            return redirect($redirectUrl);
        }

        return back();
    }

    /**
     * Gère les erreurs 404 avec SweetAlert2
     */
    protected function abort404WithSweetAlert(?string $message = null): RedirectResponse
    {
        return $this->handleErrorWithSweetAlert(404, null, $message);
    }

    /**
     * Gère les erreurs 403 avec SweetAlert2
     */
    protected function abort403WithSweetAlert(?string $message = null): RedirectResponse
    {
        return $this->handleErrorWithSweetAlert(403, null, $message);
    }

    /**
     * Gère les erreurs 500 avec SweetAlert2
     */
    protected function abort500WithSweetAlert(?Throwable $exception = null, ?string $message = null): RedirectResponse
    {
        return $this->handleErrorWithSweetAlert(500, $exception, $message);
    }

    /**
     * Gère les erreurs avec redirection personnalisée
     */
    protected function abortWithSweetAlert(
        int $statusCode,
        ?string $message = null,
        ?string $redirectUrl = null,
        ?Throwable $exception = null
    ): RedirectResponse {
        return $this->handleErrorWithSweetAlert($statusCode, $exception, $message, $redirectUrl);
    }
}
