<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Salle;

use App\Http\Rules\Uuid7Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\FlashValidationErrors;

class CreateSalleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cinema_uuid'          => ['required', 'string', new Uuid7Rule],
            'nom'                  => 'required|string|min:1|max:100',
            'capacite_totale'      => 'required|integer|min:1|max:1000',
            'nombre_rangees'       => 'required|integer|min:1|max:50',
            'places_par_rangee'    => 'required|integer|min:1|max:100',
            'places_standard'      => 'required|integer|min:0',
            'places_pmr'           => 'required|integer|min:0',
            'qualite_projection'   => 'nullable|array',
            'qualite_projection.*' => 'string',
            'qualite_sonore'       => 'nullable|array',
            'qualite_sonore.*'     => 'string',
            'climatisation'        => 'boolean',
            'accessibilite_pmr'    => 'boolean',
            'plan_salle'           => 'nullable|array',
            'statut'               => 'required|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cinema_uuid.required'       => 'Le cinéma est obligatoire.',
            'cinema_uuid.exists'         => 'Le cinéma sélectionné n\'existe pas.',
            'nom.required'               => 'Le nom de la salle est obligatoire.',
            'nom.max'                    => 'Le nom ne peut pas dépasser 100 caractères.',
            'capacite_totale.required'   => 'La capacité totale est obligatoire.',
            'capacite_totale.min'        => 'La capacité doit être d\'au moins 1 place.',
            'capacite_totale.max'        => 'La capacité ne peut dépasser 1000 places.',
            'nombre_rangees.required'    => 'Le nombre de rangées est obligatoire.',
            'nombre_rangees.min'         => 'Il doit y avoir au moins 1 rangée.',
            'places_par_rangee.required' => 'Le nombre de places par rangée est obligatoire.',
            'places_standard.required'   => 'Le nombre de places standard est obligatoire.',
            'places_pmr.required'        => 'Le nombre de places PMR est obligatoire.',
            'qualite_projection.*.in'    => 'Qualité de projection invalide.',
            'qualite_sonore.*.in'        => 'Qualité sonore invalide.',
            'statut.required'            => 'Le statut est obligatoire.',
            'statut.in'                  => 'Statut invalide.',
        ];
    }
}
