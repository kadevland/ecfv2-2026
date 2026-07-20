<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Seance;

use Illuminate\Validation\Rule;
use App\Domain\Enums\VersionFilm;
use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Enums\QualiteSonore;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Cinema\Enums\QualiteProjection;

final class UpdateSeanceRequest extends FormRequest
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
        return [
            // film_id et salle_id ne sont PAS modifiables en update
            // date_heure_fin est calculée automatiquement
            'date_seance'         => 'sometimes|date|after:today',
            'heure_debut'         => 'sometimes|date_format:H:i',
            'duree_additionnelle' => 'sometimes|integer|min:10|max:60',
            'version'             => ['sometimes', 'string', Rule::enum(VersionFilm::class)],
            // Qualités (présentes dans le formulaire)
            'qualite_projection' => ['nullable', 'string', Rule::enum(QualiteProjection::class)],
            'qualite_sonore'     => ['nullable', 'string', Rule::enum(QualiteSonore::class)],
            'placement_libre'    => 'sometimes|boolean',
            'statut'             => ['sometimes', 'string', Rule::enum(StatutSeance::class)],

            // Tarification (présente dans le formulaire)
            'tarifs'        => 'sometimes|array',
            'tarifs.normal' => 'sometimes|numeric|min:0',
            'tarifs.reduit' => 'sometimes|numeric|min:0',
            'tarifs.enfant' => 'sometimes|numeric|min:0',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_seance.after'       => 'La séance doit être programmée au minimum demain.',
            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'duree_additionnelle.min' => 'La durée additionnelle doit être d\'au moins 10 minutes.',
            'duree_additionnelle.max' => 'La durée additionnelle ne peut pas dépasser 60 minutes.',
            'version.enum'            => 'La version sélectionnée n\'est pas valide.',
            'statut.enum'             => 'Le statut sélectionné n\'est pas valide.',
            'qualite_projection.enum' => 'La qualité de projection sélectionnée n\'est pas valide.',
            'qualite_sonore.enum'     => 'La qualité sonore sélectionnée n\'est pas valide.',
            'tarifs.normal.numeric'   => 'Le tarif normal doit être un nombre.',
            'tarifs.reduit.numeric'   => 'Le tarif réduit doit être un nombre.',
            'tarifs.enfant.numeric'   => 'Le tarif enfant doit être un nombre.',
            'tarifs.*.min'            => 'Les tarifs doivent être positifs.',
        ];
    }
}
