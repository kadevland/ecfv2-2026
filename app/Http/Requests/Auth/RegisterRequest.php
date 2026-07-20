<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\CNILPasswordRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // L'accès est géré par le middleware guest
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prenom' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZàáâäçéèêëíìîïñóòôöúùûüýÿæœÀÁÂÄÇÉÈÊËÍÌÎÏÑÓÒÔÖÚÙÛÜÝŸÆŒ\s\-\']+$/',
            ],
            'nom' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZàáâäçéèêëíìîïñóòôöúùûüýÿæœÀÁÂÄÇÉÈÊËÍÌÎÏÑÓÒÔÖÚÙÛÜÝŸÆŒ\s\-\']+$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:auth.users,email',
            ],
            'telephone' => [
                'nullable',
                'string',
                'regex:/^(?:\+33|0)[1-9](?:[0-9]{8})$/',
                'unique:auth.users,telephone',
            ],
            'date_naissance' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(13)->format('Y-m-d'),
                'after:' . now()->subYears(120)->format('Y-m-d'),
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:12',
                'max:128',
                Password::defaults(),
                new CNILPasswordRule,
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
            'terms' => [
                'required',
                'accepted',
            ],
            'newsletter' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'prenom'                => 'prénom',
            'nom'                   => 'nom de famille',
            'email'                 => 'adresse e-mail',
            'telephone'             => 'numéro de téléphone',
            'date_naissance'        => 'date de naissance',
            'password'              => 'mot de passe',
            'password_confirmation' => 'confirmation du mot de passe',
            'terms'                 => 'conditions générales',
            'newsletter'            => 'inscription à la newsletter',
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Messages pour le prénom
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.min'      => 'Le prénom doit contenir au moins :min caractères.',
            'prenom.max'      => 'Le prénom ne peut pas dépasser :max caractères.',
            'prenom.regex'    => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes.',

            // Messages pour le nom
            'nom.required' => 'Le nom de famille est obligatoire.',
            'nom.min'      => 'Le nom doit contenir au moins :min caractères.',
            'nom.max'      => 'Le nom ne peut pas dépasser :max caractères.',
            'nom.regex'    => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',

            // Messages pour l'email
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email'    => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'   => 'Cette adresse e-mail est déjà utilisée.',
            'email.max'      => 'L\'adresse e-mail ne peut pas dépasser :max caractères.',

            // Messages pour le téléphone
            'telephone.regex'  => 'Le numéro de téléphone n\'est pas au bon format (ex: 06 12 34 56 78 ou +33 6 12 34 56 78).',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',

            // Messages pour la date de naissance
            'date_naissance.required'        => 'La date de naissance est obligatoire.',
            'date_naissance.date'            => 'La date de naissance n\'est pas valide.',
            'date_naissance.before_or_equal' => 'Vous devez avoir au moins 13 ans pour vous inscrire.',
            'date_naissance.after'           => 'La date de naissance ne peut pas être antérieure à :date.',

            // Messages pour le mot de passe
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.max'       => 'Le mot de passe ne peut pas dépasser :max caractères.',

            // Messages pour la confirmation du mot de passe
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',

            // Messages pour les conditions
            'terms.required' => 'Vous devez accepter les conditions générales.',
            'terms.accepted' => 'Vous devez accepter les conditions générales.',
        ];
    }

    /**
     * Configure les options pour Password::defaults()
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // Ajouter des logs pour débugger en développement
        if (app()->environment('local')) {
            logger()->info('Validation échouée pour l\'inscription', [
                'errors' => $validator->errors()->toArray(),
                'input'  => $this->except(['password', 'password_confirmation']),
            ]);
        }

        parent::failedValidation($validator);
    }

    /**
     * Préparer les données pour validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Nettoyer le téléphone (supprimer les espaces)
            'telephone' => $this->telephone ? preg_replace('/\s+/', '', $this->telephone) : null,
            // Nettoyer l'email (trim + lowercase)
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            // Nettoyer les noms (trim + ucfirst)
            'prenom' => $this->prenom ? ucfirst(trim($this->prenom)) : null,
            'nom'    => $this->nom ? strtoupper(trim($this->nom)) : null,
        ]);
    }
}
