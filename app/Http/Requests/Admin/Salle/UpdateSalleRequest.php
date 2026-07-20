<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Salle;

use Illuminate\Validation\Rule;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Http\Requests\Traits\FlashValidationErrors;

/**
 * @property-read string $salleUuid
 * @property-read string|null $nom
 * @property-read int|null $numero
 * @property-read int|null $capaciteTotale
 * @property-read array<string>|null $technologies
 * @property-read bool|null $accessibilitePmr
 * @property-read bool|null $climatisation
 * @property-read string|null $qualiteSon
 * @property-read string|null $tailleEcran
 * @property-read string|null $typeEcran
 * @property-read array<string, mixed>|null $configurationSieges
 * @property-read float|null $tarifSupplement
 * @property-read string|null $maintenanceProgrammee
 * @property-read string|null $statut
 */
final class UpdateSalleRequest extends FormRequest
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
            'nom'                  => 'nullable|string|min:1|max:100',
            'capacite_totale'      => 'nullable|integer|min:1|max:1000',
            'qualite_projection'   => 'nullable|array',
            'qualite_projection.*' => ['string', Rule::enum(QualiteProjection::class)],
            'qualite_sonore'       => 'nullable|array',
            'qualite_sonore.*'     => ['string', Rule::enum(QualiteSonore::class)],
            'accessibilite_pmr'    => 'nullable|boolean',
            'climatisation'        => 'nullable|boolean',
            'plan_salle'           => 'nullable|array',
            'statut'               => ['nullable', Rule::enum(StatutSalle::class)],

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
            'salleUuid.required' => 'L\'UUID de la salle est obligatoire.',
            'salleUuid.uuid'     => 'L\'UUID de la salle n\'est pas valide.',
            'nom.min'            => 'Le nom doit contenir au moins 1 caract�re.',
            'nom.max'            => 'Le nom ne peut pas d�passer 100 caract�res.',
            // 'numero.min'                         => 'Le numéro doit étre au minimum 1.',
            // 'numero.max'                         => 'Le numéro ne peut pas d�passer 999.',
            'capacite_totale.min'       => 'La capacité doit être au minimum 1.',
            'capacite_totale.max'       => 'La capacité ne peut pas dépasser 1000.',
            'qualite_projection.*.enum' => 'Qualité de projection invalide.',
            'qualite_sonore.*.enum'     => 'Qualité sonore invalide.',
            'statut.in'                 => 'Statut invalide.',
        ];
    }
}
