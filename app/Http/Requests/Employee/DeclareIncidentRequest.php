<?php

declare(strict_types=1);

namespace App\Http\Requests\Employee;

use App\Http\Rules\Uuid7Rule;
use Illuminate\Validation\Rule;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\SeveriteIncident;
use Illuminate\Foundation\Http\FormRequest;

class DeclareIncidentRequest extends FormRequest
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
            'type_incident' => [
                'required',
                Rule::enum(TypeIncident::class),
            ],
            'severite' => [
                'required',
                Rule::enum(SeveriteIncident::class),
            ],
            'titre' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
            'salle_uuid' => [
                'nullable',
                'string',
                new Uuid7Rule,
            ],
            'pieces_jointes' => [
                'nullable',
                'array',
                'max:5',
            ],
            'pieces_jointes.*' => [
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:10240', // 10MB max
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type_incident.required' => 'Le type d\'incident est requis',
            'type_incident.enum'     => 'Le type d\'incident est invalide',
            'severite.required'      => 'La sévérité est requise',
            'severite.enum'          => 'La sévérité est invalide',
            'titre.required'         => 'Le titre est requis',
            'titre.min'              => 'Le titre doit faire au moins 3 caractères',
            'titre.max'              => 'Le titre ne peut dépasser 255 caractères',
            'description.required'   => 'La description est requise',
            'description.min'        => 'La description doit faire au moins 10 caractères',
            'description.max'        => 'La description ne peut dépasser 5000 caractères',
            'salle_uuid.uuid'        => 'L\'identifiant de la salle est invalide',
            'salle_uuid.exists'      => 'La salle spécifiée n\'existe pas',
            'pieces_jointes.array'   => 'Les pièces jointes doivent être un tableau',
            'pieces_jointes.max'     => 'Vous ne pouvez pas joindre plus de 5 fichiers',
            'pieces_jointes.*.file'  => 'Chaque pièce jointe doit être un fichier',
            'pieces_jointes.*.mimes' => 'Les pièces jointes doivent être de type: jpg, jpeg, png, pdf, doc, docx',
            'pieces_jointes.*.max'   => 'Chaque fichier ne doit pas dépasser 10MB',
        ];
    }
}
