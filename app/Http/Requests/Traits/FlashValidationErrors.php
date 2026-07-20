<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FlashValidationErrors
{
    protected function failedValidation(Validator $validator): void
    {
        flash()->error('Erreur dans votre formulaire. Veuillez corriger les champs indiqués.');

        throw new HttpResponseException(
            back()->withErrors($validator)->withInput()
        );
    }
}
