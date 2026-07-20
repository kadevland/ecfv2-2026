<?php

declare(strict_types=1);

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rule;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\SeveriteIncident;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Employé authentifié peut créer incidents
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type_incident' => ['required', Rule::enum(TypeIncident::class)],
            'severite'      => ['required', Rule::enum(SeveriteIncident::class)],
            'titre'         => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string', 'max:2000'],
            'salle_db_id'   => ['nullable', 'integer'],
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
            'type_incident.required' => 'Le type d\'incident est obligatoire.',
            'type_incident.in'       => 'Le type d\'incident sélectionné n\'est pas valide.',
            'severite.required'      => 'La sévérité est obligatoire.',
            'severite.in'            => 'La sévérité sélectionnée n\'est pas valide.',
            'titre.required'         => 'Le titre est obligatoire.',
            'titre.max'              => 'Le titre ne peut pas dépasser 200 caractères.',
            'description.required'   => 'La description est obligatoire.',
            'description.max'        => 'La description ne peut pas dépasser 2000 caractères.',
            'salle_db_id.integer'    => 'L\'identifiant de salle doit être un nombre.',
        ];
    }
}
