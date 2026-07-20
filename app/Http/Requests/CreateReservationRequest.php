<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'seance_id' => 'required|string',
        ];

        // Validation pour le nouveau format multi-tarifs
        if ($this->has('places') && is_array($this->places)) {
            // Si des places par tarif sont fournies
            $rules['places']        = 'array|required';
            $rules['places.normal'] = 'integer|min:0|max:10';
            $rules['places.reduit'] = 'integer|min:0|max:10';
            $rules['places.enfant'] = 'integer|min:0|max:10';
        } elseif ($this->has('seats') && is_array($this->seats)) {
            // Si des places numérotées sont fournies (ancien format)
            $rules['seats']   = 'array|min:1|max:10';
            $rules['seats.*'] = 'string|distinct';
        } elseif ($this->has('nombre_places')) {
            // Si un nombre de places est fourni (ancien format)
            $rules['nombre_places'] = 'integer|min:1|max:10';
        } else {
            // Au moins un des formats doit être présent
            $rules['places']        = 'required_without_all:seats,nombre_places|array';
            $rules['seats']         = 'required_without_all:places,nombre_places|array';
            $rules['nombre_places'] = 'required_without_all:places,seats|integer';
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validation personnalisée pour s'assurer qu'au moins une place est sélectionnée
            if ($this->has('places') && is_array($this->places)) {
                $total = 0;
                foreach (['normal', 'reduit', 'enfant'] as $tarif) {
                    $total += (int) ($this->places[$tarif] ?? 0);
                }

                if ($total === 0) {
                    $validator->errors()->add('places', 'Veuillez sélectionner au moins une place');
                }

                if ($total > 10) {
                    $validator->errors()->add('places', 'Maximum 10 places au total');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'seance_id.required'                 => 'La séance est obligatoire',
            'seats.required_without_all'         => 'Veuillez sélectionner des places ou indiquer le nombre souhaité',
            'seats.array'                        => 'Format de sélection des places invalide',
            'seats.min'                          => 'Veuillez sélectionner au moins une place',
            'seats.max'                          => 'Maximum 10 places par réservation',
            'seats.*.distinct'                   => 'Vous ne pouvez pas sélectionner la même place plusieurs fois',
            'nombre_places.required_without_all' => 'Veuillez indiquer le nombre de places ou sélectionner des sièges',
            'nombre_places.integer'              => 'Le nombre de places doit être un nombre entier',
            'nombre_places.min'                  => 'Vous devez réserver au moins une place',
            'nombre_places.max'                  => 'Maximum 10 places par réservation',
            'places.required_without_all'        => 'Veuillez sélectionner des places',
            'places.normal.integer'              => 'Le nombre de places normales doit être un nombre entier',
            'places.reduit.integer'              => 'Le nombre de places réduites doit être un nombre entier',
            'places.enfant.integer'              => 'Le nombre de places enfants doit être un nombre entier',
            'places.normal.max'                  => 'Maximum 10 places par tarif',
            'places.reduit.max'                  => 'Maximum 10 places par tarif',
            'places.enfant.max'                  => 'Maximum 10 places par tarif',
        ];
    }
}
