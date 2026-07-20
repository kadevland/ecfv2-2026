<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Seance;

use Illuminate\Validation\Rule;
use App\Domain\Enums\VersionFilm;
use App\Domain\Cinema\Enums\QualiteSonore;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Cinema\Enums\QualiteProjection;

final class CreateSeanceRequest extends FormRequest
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
            // Champs REQUIS pour la création
            'film_id'             => 'required|string',
            'salle_id'            => 'required|string',
            'date_seance'         => 'required|date|after:today',
            'heure_debut'         => 'required|date_format:H:i',
            'duree_additionnelle' => 'required|integer|min:10|max:60',
            'version'             => ['required', 'string', Rule::enum(VersionFilm::class)],

            // Champ calculé côté frontend pour compatibilité
            'date_heure_debut' => 'required|date|after:now',
            // PAS de date_heure_fin - elle est calculée dans le handler

            // Qualités (présentes dans le formulaire)
            'qualite_projection' => ['nullable', 'string', Rule::enum(QualiteProjection::class)],
            'qualite_sonore'     => ['nullable', 'string', Rule::enum(QualiteSonore::class)],

            // Tarification (présente dans le formulaire)
            'tarifs'        => 'required|array',
            'tarifs.normal' => 'required|numeric|min:0',
            'tarifs.reduit' => 'required|numeric|min:0',
            'tarifs.enfant' => 'required|numeric|min:0',

            // Optionnel
            'placement_libre' => 'boolean',

            // PAS de statut (toujours PROGRAMMEE à la création)
            // PAS de qualité projection/sonore (pas dans le formulaire)
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'film_id.required'             => 'Le film est obligatoire.',
            'salle_id.required'            => 'La salle est obligatoire.',
            'date_seance.required'         => 'La date de la séance est obligatoire.',
            'date_seance.after'            => 'La séance doit être programmée au minimum demain.',
            'heure_debut.required'         => 'L\'heure de début est obligatoire.',
            'heure_debut.date_format'      => 'L\'heure de début doit être au format HH:MM.',
            'duree_additionnelle.required' => 'La durée additionnelle est obligatoire.',
            'duree_additionnelle.min'      => 'La durée additionnelle doit être d\'au moins 10 minutes.',
            'duree_additionnelle.max'      => 'La durée additionnelle ne peut pas dépasser 60 minutes.',
            'date_heure_debut.required'    => 'La date et heure de début sont obligatoires.',
            'date_heure_debut.after'       => 'La séance doit être programmée dans le futur.',
            'version.required'             => 'La version du film est obligatoire.',
            'version.enum'                 => 'La version sélectionnée n\'est pas valide.',

            // Messages tarification
            'tarifs.required'        => 'La tarification est obligatoire.',
            'tarifs.normal.required' => 'Le tarif normal est obligatoire.',
            'tarifs.normal.numeric'  => 'Le tarif normal doit être un nombre.',
            'tarifs.reduit.required' => 'Le tarif réduit est obligatoire.',
            'tarifs.reduit.numeric'  => 'Le tarif réduit doit être un nombre.',
            'tarifs.enfant.required' => 'Le tarif enfant est obligatoire.',
            'tarifs.enfant.numeric'  => 'Le tarif enfant doit être un nombre.',
            'tarifs.*.min'           => 'Les tarifs doivent être positifs.',

            // Messages qualités
            'qualite_projection.enum' => 'La qualité de projection sélectionnée n\'est pas valide.',
            'qualite_sonore.enum'     => 'La qualité sonore sélectionnée n\'est pas valide.',
        ];
    }
}
