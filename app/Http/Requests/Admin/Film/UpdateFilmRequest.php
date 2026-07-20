<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Film;

use JsonException;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\FlashValidationErrors;

/**
 * @property-read string|null $titre
 * @property-read string|null $titre_original
 * @property-read string|null $synopsis
 * @property-read array<string>|null $genre
 * @property-read array<string>|null $realisateurs
 * @property-read array<string>|string|null $acteurs_principaux
 * @property-read int|null $duree_minutes
 * @property-read string|null $date_sortie
 * @property-read string|null $pays_origine
 * @property-read string|null $langue_originale
 * @property-read string|null $classification
 * @property-read string|null $producteur
 * @property-read string|null $affiche_url
 * @property-read string|null $bande_annonce_url
 * @property-read array<string>|null $images_additionnelles
 * @property-read float|null $note_critique
 * @property-read float|null $note_public
 * @property-read string|null $statut
 * @property-read bool|null $est_actif
 * @property-read array<string, mixed>|null $metadonnees_techniques
 */
final class UpdateFilmRequest extends FormRequest
{
    use FlashValidationErrors;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize () : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules () : array
    {
        return [
            'titre'                   => 'sometimes|required|string|min:1|max:300',
            'titre_original'          => 'sometimes|nullable|string|min:1|max:300',
            'synopsis'                => 'sometimes|nullable|string',
            'genre'                   => 'required|array|min:1',
            'genre.*'                 => [ Rule::enum(\App\Domain\Enums\GenreFilm::class)],
            'realisateurs'            => 'sometimes|required|array|min:1',
            'realisateurs.*'          => 'string|min:2|max:100',
            'acteurs_principaux'      => 'sometimes|nullable|array',
            'acteurs_principaux.*'    => 'string|min:2|max:100',
            'duree_minutes'           => 'sometimes|required|integer|min:1|max:600',
            'date_sortie'             => 'sometimes|required|date|date_format:Y-m-d',
            'pays_origine'            => 'sometimes|required|string|min:2|max:100',
            'langue_originale'        => 'sometimes|required|string|min:2|max:50',
            'classification'          => ['required', Rule::enum(\App\Domain\Enums\ClassificationFilm::class)],
            'producteur'              => 'sometimes|nullable|string|min:2|max:200',
            'affiche_url'             => 'sometimes|nullable|url|max:500',
            'bande_annonce_url'       => 'sometimes|nullable|url|max:500',
            'images_additionnelles'   => 'sometimes|nullable|array',
            'images_additionnelles.*' => 'url|max:500',
            'note_critique'           => 'sometimes|nullable|numeric|between:0,10',
            'note_public'             => 'sometimes|nullable|numeric|between:0,10',
            'statut'                  => 'sometimes|required|string|in:A_VENIR,EN_SALLE,ARCHIVE,SUSPENDU',
            'est_actif'               => 'sometimes|boolean',
            'metadonnees_techniques'  => 'sometimes|nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages () : array
    {
        return [
            'titre.min'                   => 'Le titre doit contenir au moins 1 caractère.',
            'titre.max'                   => 'Le titre ne peut pas dépasser 300 caractères.',
            'titre_original.max'          => 'Le titre original ne peut pas dépasser 300 caractères.',
            'realisateurs.min'            => 'Au moins un réalisateur est requis.',
            'realisateurs.*.min'          => 'Le nom du réalisateur doit contenir au moins 2 caractères.',
            'realisateurs.*.max'          => 'Le nom du réalisateur ne peut pas dépasser 100 caractères.',
            'genre'                       => 'Le genre du film est obligatoire.',
            'genre.*'                     => 'Genre invalide.',
            'duree_minutes.min'           => 'La durée doit être d\'au moins 1 minute.',
            'duree_minutes.max'           => 'La durée ne peut pas dépasser 600 minutes.',
            'date_sortie.date'            => 'La date de sortie doit être une date valide.',
            'date_sortie.date_format'     => 'La date de sortie doit être au format AAAA-MM-JJ.',
            'pays_origine.min'            => 'Le pays d\'origine doit contenir au moins 2 caractères.',
            'pays_origine.max'            => 'Le pays d\'origine ne peut pas dépasser 100 caractères.',
            'langue_originale.min'        => 'La langue originale doit contenir au moins 2 caractères.',
            'langue_originale.max'        => 'La langue originale ne peut pas dépasser 50 caractères.',
            'classification.enum'         => 'Classification invalide.',
            'producteur.min'              => 'Le nom du producteur doit contenir au moins 2 caractères.',
            'producteur.max'              => 'Le nom du producteur ne peut pas dépasser 200 caractères.',
            'affiche_url.url'             => 'L\'URL de l\'affiche n\'est pas valide.',
            'affiche_url.max'             => 'L\'URL de l\'affiche ne peut pas dépasser 500 caractères.',
            'bande_annonce_url.url'       => 'L\'URL de la bande-annonce n\'est pas valide.',
            'bande_annonce_url.max'       => 'L\'URL de la bande-annonce ne peut pas dépasser 500 caractères.',
            'images_additionnelles.*.url' => 'Chaque URL d\'image additionnelle doit être valide.',
            'images_additionnelles.*.max' => 'Chaque URL d\'image ne peut pas dépasser 500 caractères.',
            'note_critique.between'       => 'La note critique doit être entre 0 et 10.',
            'note_public.between'         => 'La note public doit être entre 0 et 10.',
            'statut.in'                   => 'Le statut doit être : À venir, En salle, Archivé ou Suspendu.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
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
