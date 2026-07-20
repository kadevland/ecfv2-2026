<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Film;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\FlashValidationErrors;
use JsonException;


class CreateFilmRequest extends FormRequest
{
    use FlashValidationErrors;

    public function authorize () : bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules () : array
    {
        return [
            'titre'                => 'required|string|min:1|max:300',
            'titre_original'       => 'nullable|string|min:1|max:300',
            'synopsis'             => 'nullable|string|max:5000',
            'genre'                => 'required|array|min:1',
            'genre.*'              => [Rule::enum(\App\Domain\Enums\GenreFilm::class)],
            'realisateurs'         => 'sometimes|required|array|min:1',
            'realisateurs.*'       => 'string|min:2|max:100',
            'acteurs_principaux'   => 'sometimes|nullable|array',
            'acteurs_principaux.*' => 'string|min:2|max:100',
            'duree_minutes'        => 'required|numeric|min:1|max:600', // numeric instead of integer
            'date_sortie'          => 'required|date|date_format:Y-m-d',
            'pays_origine'         => 'nullable|string|min:2|max:100',
            'langue_originale'     => 'required|string|min:2|max:50',
            'classification'       => ['required', 'string', Rule::enum(\App\Domain\Enums\ClassificationFilm::class)],
            'producteur'           => 'nullable|string|min:2|max:200',
            'affiche_url'          => 'nullable|url|max:500',
            'bande_annonce_url'    => 'nullable|url|max:500',
            'note_critique'        => 'nullable|numeric|between:0,10',
            'note_public'          => 'nullable|numeric|between:0,10',
            'statut'               => 'nullable|string|in:A_VENIR,EN_SALLE,ARCHIVE,SUSPENDU',
            'est_actif'            => 'nullable', // Accept any value, mapper will handle conversion
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages () : array
    {
        return [
            'titre.required'            => 'Le titre du film est obligatoire.',
            'titre.min'                 => 'Le titre doit contenir au moins 1 caractère.',
            'titre.max'                 => 'Le titre ne peut pas dépasser 255 caractères.',
            'realisateurs.required'     => 'Au moins un réalisateur est requis.',
            'realisateurs.min'          => 'Au moins un réalisateur est requis.',
            'realisateurs.*.required'   => 'Le nom du réalisateur est obligatoire.',
            'realisateurs.*.max'        => 'Le nom du réalisateur ne peut pas dépasser 100 caractères.',
            'genre'                     => 'Le genre du film est obligatoire.',
            'genre.*'                   => 'Genre invalide.',
            'duree_minutes.required'    => 'La durée du film est obligatoire.',
            'duree_minutes.integer'     => 'La durée doit être un nombre entier.',
            'duree_minutes.min'         => 'La durée doit être d\'au moins 1 minute.',
            'duree_minutes.max'         => 'La durée ne peut pas dépasser 1000 minutes.',
            'classification.required'   => 'La classification est obligatoire.',
            'classification.enum'       => 'Classification invalide.',
            'date_sortie.required'      => 'La date de sortie est obligatoire.',
            'date_sortie.date'          => 'La date de sortie doit être une date valide.',
            'pays_origine.required'     => 'Le pays d\'origine est obligatoire.',
            'langue_originale.required' => 'La langue originale est obligatoire.',
            'note_critique.numeric'     => 'La note critique doit être un nombre.',
            'note_critique.between'     => 'La note critique doit être entre 0 et 10.',
            'note_public.numeric'       => 'La note public doit être un nombre.',
            'note_public.between'       => 'La note public doit être entre 0 et 10.',
            'affiche_url.url'           => 'L\'URL de l\'affiche n\'est pas valide.',
            'bande_annonce_url.url'     => 'L\'URL de la bande-annonce n\'est pas valide.',
        ];
    }

    protected function prepareForValidation () : void
    {
        $data = [];

        //dd($this->all());

        // Convert textarea realisateurs to array
        if ($this->has('realisateurs') && is_string($this->input('realisateurs'))) {
            $realisateurs         = array_filter(
                array_map('trim', explode("\n", $this->input('realisateurs'))),
                fn ($item) => !empty($item)
            );
            $data['realisateurs'] = $realisateurs;
        }

        if ($this->has('acteurs_principaux') && is_string($this->input('acteurs_principaux'))) {
            $acteurs_principaux         = array_filter(
                array_map('trim', explode("\n", $this->input('acteurs_principaux'))),
                fn ($item) => !empty($item)
            );
            $data['acteurs_principaux'] = $acteurs_principaux;
        }

        if ($this->has('est_actif')) {
            $data['est_actif'] = $this->boolean('est_actif');
        }

        if ($this->has('duree_minutes')) {
            $data['duree_minutes'] = (int) $this->input('duree_minutes');
        }

        if ($this->has('note_critique')) {
            $data['note_critique'] = (float) $this->input('note_critique');
        }

        if ($this->has('note_public')) {
            $data['note_public'] = (float) $this->input('note_public');
        }

        // Traitement des images additionnelles
        if ($this->has('images_additionnelles') && is_string($this->input('images_additionnelles'))) {
            $images                        = array_filter(
                array_map('trim', explode("\n", $this->input('images_additionnelles'))),
                fn ($item) => !empty($item)
            );
            $data['images_additionnelles'] = $images;
        }

        // Traitement des métadonnées techniques (optionnel)
        if ($this->has('metadonnees_techniques') && is_string($this->input('metadonnees_techniques'))) {
            try {
                $metadonnees                    = json_decode($this->input('metadonnees_techniques'), true, 512, JSON_THROW_ON_ERROR);
                $data['metadonnees_techniques'] = $metadonnees;
            } catch (JsonException $e) {
                // Si ce n'est pas du JSON valide, on ignore
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
