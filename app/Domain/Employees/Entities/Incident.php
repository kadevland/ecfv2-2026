<?php

declare(strict_types=1);

namespace App\Domain\Employees\Entities;

use DateTime;
use DomainException;
use DateTimeInterface;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\Entities\AggregateRoot;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Domain\Employees\ValueObjects\IncidentId;

final class Incident extends AggregateRoot
{
    public readonly IncidentId $id;

    public private(set) EmploiId $emploiDeclarantId;

    public private(set) CinemaId $cinemaId;

    public private(set) ?SalleId $salleId;

    public private(set) string $typeIncident;

    public private(set) string $severite;

    public private(set) string $titre;

    public private(set) string $description;

    public private(set) string $statut;

    public private(set) ?DateTimeInterface $dateResolution;

    public private(set) ?string $responsableResolution;

    /** @var array<string, mixed>|null */
    public private(set) ?array $piecesJointes;

    /**
     * @param array<string, mixed>|null $piecesJointes
     */
    public function __construct(
        IncidentId $id,
        EmploiId $emploiDeclarantId,
        CinemaId $cinemaId,
        string $typeIncident,
        string $severite,
        string $titre,
        string $description,
        ?SalleId $salleId = null,
        string $statut = 'ouvert',
        ?DateTimeInterface $dateResolution = null,
        ?string $responsableResolution = null,
        ?array $piecesJointes = null,
    ) {
        $this->id                    = $id;
        $this->emploiDeclarantId     = $emploiDeclarantId;
        $this->cinemaId              = $cinemaId;
        $this->salleId               = $salleId;
        $this->typeIncident          = $typeIncident;
        $this->severite              = $severite;
        $this->titre                 = $titre;
        $this->description           = $description;
        $this->statut                = $statut;
        $this->dateResolution        = $dateResolution;
        $this->responsableResolution = $responsableResolution;
        $this->piecesJointes         = $piecesJointes;
    }

    public static function declarer(
        EmploiId $emploiDeclarantId,
        CinemaId $cinemaId,
        string $typeIncident,
        string $severite,
        string $titre,
        string $description,
        ?SalleId $salleId = null,
    ): self {
        return new self(
            IncidentId::generate(),
            $emploiDeclarantId,
            $cinemaId,
            $typeIncident,
            $severite,
            $titre,
            $description,
            $salleId,
        );
    }

    public function isOpen(): bool
    {
        return $this->statut === 'ouvert';
    }

    public function isInProgress(): bool
    {
        return $this->statut === 'en_cours';
    }

    public function isResolved(): bool
    {
        return $this->statut === 'resolu';
    }

    public function isClosed(): bool
    {
        return $this->statut === 'ferme';
    }

    public function isCritical(): bool
    {
        return $this->severite === 'critique';
    }

    public function assigner(string $responsableId): void
    {
        if ($this->isClosed()) {
            throw new DomainException('Impossible d\'assigner un incident fermé');
        }

        $this->responsableResolution = $responsableId;
        $this->statut                = 'en_cours';
    }

    public function resoudre(?string $responsableId = null): void
    {
        if ($this->isClosed()) {
            throw new DomainException('Impossible de résoudre un incident fermé');
        }

        $this->statut                = 'resolu';
        $this->dateResolution        = new DateTime;
        $this->responsableResolution = $responsableId ?? $this->responsableResolution;
    }

    public function fermer(): void
    {
        if (!$this->isResolved()) {
            throw new DomainException('Un incident doit être résolu avant d\'être fermé');
        }

        $this->statut = 'ferme';
    }

    public function reouvrir(): void
    {
        if (!$this->isClosed() && !$this->isResolved()) {
            throw new DomainException('Seuls les incidents résolus ou fermés peuvent être rouverts');
        }

        $this->statut         = 'ouvert';
        $this->dateResolution = null;
    }

    public function ajouterPieceJointe(string $filename, string $path, ?string $type = null): void
    {
        $piecesJointes = $this->piecesJointes ?? [];

        $piecesJointes[] = [
            'filename'    => $filename,
            'path'        => $path,
            'type'        => $type,
            'uploaded_at' => (new DateTime)->format('c'),
        ];

        $this->piecesJointes = $piecesJointes;
    }
}
