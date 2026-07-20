<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use Illuminate\Validation\Rule;
use App\Domain\Shared\Enums\SexeEnum;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\FlashValidationErrors;

class UpdateClientRequest extends FormRequest
{
    use FlashValidationErrors;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prenom'         => 'sometimes|required|string|min:2|max:50',
            'nom'            => 'sometimes|required|string|min:2|max:50',
            'email'          => 'sometimes|required|email|max:255',
            'telephone'      => 'sometimes|nullable|string|max:20',
            'date_naissance' => 'sometimes|nullable|date|before:today',
            'sexe'           => ['sometimes', 'nullable', Rule::in(SexeEnum::values())],
            'adresse'        => 'sometimes|nullable|string|max:200',
            'ville'          => 'sometimes|nullable|string|max:100',
            'code_postal'    => 'sometimes|nullable|string|max:10',
            'pays'           => 'sometimes|nullable|string|max:100',
            'estActif'       => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prenom.required'       => 'Le prénom est obligatoire.',
            'prenom.min'            => 'Le prénom doit contenir au moins 2 caractères.',
            'nom.required'          => 'Le nom est obligatoire.',
            'nom.min'               => 'Le nom doit contenir au moins 2 caractères.',
            'email.required'        => 'L\'email est obligatoire.',
            'email.email'           => 'L\'email doit être valide.',
            'email.unique'          => 'Cet email est déjà utilisé.',
            'date_naissance.date'   => 'La date de naissance doit être une date valide.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'sexe.in'               => 'Le sexe doit être : ' . implode(', ', SexeEnum::values()) . '.',
        ];
    }
}
