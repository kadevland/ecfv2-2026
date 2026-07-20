<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Cinema;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\FlashValidationErrors;

class UpdateCinemaRequest extends FormRequest
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
        $rules = [
            'nom'         => 'sometimes|required|string|min:2|max:100',
            'rue'         => 'sometimes|required|string|min:5|max:200',
            'ville'       => 'sometimes|required|string|min:2|max:100',
            'code_postal' => 'sometimes|required|string|min:4|max:10',
            'pays'        => 'sometimes|required|string|size:2',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'telephone'   => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'est_actif'   => 'sometimes|boolean',
            'horaires'    => 'sometimes|array',
        ];

        // Validation spécifique pour chaque jour
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

        foreach ($jours as $jour) {
            // Validation technique simple - la logique métier sera dans le VO
            $rules["horaires.{$jour}"]                        = 'nullable|array';
            $rules["horaires.{$jour}.ouvert"]                 = 'nullable|boolean';
            $rules["horaires.{$jour}.debut_matin"]            = 'nullable|date_format:H:i';
            $rules["horaires.{$jour}.fin_matin"]              = 'nullable|date_format:H:i|after_or_equal:horaires.' . $jour . '.debut_matin';
            $rules["horaires.{$jour}.duree_max_seance_matin"] = 'nullable|integer|min:0|max:480';
            $rules["horaires.{$jour}.debut_apres"]            = 'nullable|date_format:H:i|after_or_equal:horaires.' . $jour . '.fin_matin';
            $rules["horaires.{$jour}.fin_apres"]              = 'nullable|date_format:H:i|after_or_equal:horaires.' . $jour . '.debut_apres';
            $rules["horaires.{$jour}.duree_max_seance_apres"] = 'nullable|integer|min:0|max:480';

        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required'                               => 'Le nom du cinéma est obligatoire.',
            'nom.min'                                    => 'Le nom doit contenir au moins 2 caractères.',
            'nom.max'                                    => 'Le nom ne peut pas dépasser 100 caractères.',
            'rue.required'                               => 'L\'adresse est obligatoire.',
            'rue.min'                                    => 'L\'adresse doit contenir au moins 5 caractères.',
            'ville.required'                             => 'La ville est obligatoire.',
            'code_postal.required'                       => 'Le code postal est obligatoire.',
            'pays.required'                              => 'Le pays est obligatoire.',
            'pays.size'                                  => 'Le pays doit être un code à 2 caractères (ex: FR, BE).',
            'latitude.required'                          => 'La latitude est obligatoire.',
            'latitude.between'                           => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.required'                         => 'La longitude est obligatoire.',
            'longitude.between'                          => 'La longitude doit être comprise entre -180 et 180.',
            'email.email'                                => 'L\'adresse email n\'est pas valide.',
            'site_web.url'                               => 'L\'URL du site web n\'est pas valide.',
            'description.max'                            => 'La description ne peut pas dépasser 1000 caractères.',
            'horaires.*.debut_matin.required'            => 'L\'heure de début matin est obligatoire.',
            'horaires.*.fin_matin.required'              => 'L\'heure de fin matin est obligatoire.',
            'horaires.*.duree_max_seance_matin.required' => 'La durée max des séances matin est obligatoire.',
            'horaires.*.duree_max_seance_matin.min'      => 'La durée max doit être positive.',
            'horaires.*.duree_max_seance_matin.max'      => 'La durée max ne peut dépasser 8h (480 min).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void {}
}
