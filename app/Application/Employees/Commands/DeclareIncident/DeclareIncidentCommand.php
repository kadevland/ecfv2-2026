<?php

declare(strict_types=1);

namespace App\Application\Employees\Commands\DeclareIncident;

use Exception;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\SeveriteIncident;
use Respect\Validation\Validator as v;
use App\Application\Contracts\CommandInterface;

final readonly class DeclareIncidentCommand implements CommandInterface
{
    public function __construct(
        public string $emploiDeclarantUuid,
        public string $cinemaUuid,
        public TypeIncident $typeIncident,
        public SeveriteIncident $severite,
        public string $titre,
        public string $description,
        public ?string $salleUuid = null,
        /** @var array<array{filename: string, path: string, type?: string}>|null */ public ?array $piecesJointes = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        // Validation emploiDeclarantUuid
        try {
            v::uuidV7()->assert($this->emploiDeclarantUuid);
        } catch (Exception) {
            $errors['emploiDeclarantUuid'] = 'UUID de l\'employé déclarant invalide';
        }

        // Validation cinemaUuid
        try {
            v::uuidV7()->assert($this->cinemaUuid);
        } catch (Exception) {
            $errors['cinemaUuid'] = 'UUID du cinéma invalide';
        }

        // Validation salleUuid (optionnel)
        if ($this->salleUuid !== null) {
            try {
                v::uuidV7()->assert($this->salleUuid);
            } catch (Exception) {
                $errors['salleUuid'] = 'UUID de la salle invalide';
            }
        }

        // Validation titre
        try {
            v::stringType()->notEmpty()->length(3, 255)->assert($this->titre);
        } catch (Exception) {
            $errors['titre'] = 'Le titre doit faire entre 3 et 255 caractères';
        }

        // Validation description
        try {
            v::stringType()->notEmpty()->length(10, 5000)->assert($this->description);
        } catch (Exception) {
            $errors['description'] = 'La description doit faire entre 10 et 5000 caractères';
        }

        // Validation pièces jointes
        if ($this->piecesJointes !== null && !empty($this->piecesJointes)) {
            foreach ($this->piecesJointes as $index => $piece) {
                if (empty($piece['filename']) || empty($piece['path'])) {
                    $errors["piecesJointes.$index"] = 'Pièce jointe invalide (filename et path requis)';
                }
            }
        }

        return $errors;
    }
}
