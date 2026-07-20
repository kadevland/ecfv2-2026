<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Employees;

use App\Infrastructure\Casts\AsMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;

/**
 * @property string $id
 * @property string $emploi_id
 * @property string $type_contrat
 * @property string $temps_travail
 * @property \Money\Money|null $salaire_base
 * @property string|null $salaire_base_devise
 * @property \Money\Money|null $salaire_horaire
 * @property string|null $salaire_horaire_devise
 * @property array<string, mixed>|null $primes
 * @property array<string, mixed>|null $horaires_type
 * @property float $conges_solde
 * @property \Illuminate\Support\Carbon $date_debut_contrat
 * @property \Illuminate\Support\Carbon|null $date_fin_contrat
 * @property bool $est_actif
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Emploi $emploi
 */
final class Contrat extends Model
{
    use HasFactory;

    protected $connection = ContratSchema::CONNECTION;

    protected $table = ContratSchema::FULL_TABLE;

    protected $primaryKey = ContratSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        ContratSchema::ID,
        ContratSchema::EMPLOI_UUID,
        ContratSchema::TYPE_CONTRAT,
        ContratSchema::TEMPS_TRAVAIL,
        'salaire_base',
        'salaire_base_devise',
        'salaire_horaire',
        'salaire_horaire_devise',
        'primes',
        ContratSchema::HORAIRES_STANDARDS,
        'conges_solde',
        ContratSchema::DATE_DEBUT,
        ContratSchema::DATE_FIN,
        'est_actif',
    ];

    /**
     * Get the emploi that owns this contrat.
     *
     * @return BelongsTo<Emploi, $this>
     */
    public function emploi(): BelongsTo
    {
        /** @var BelongsTo<Emploi, $this> */
        return $this->belongsTo(Emploi::class, ContratSchema::EMPLOI_UUID, EmploiSchema::ID);
    }

    /**
     * Check if contract is currently active
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();

        return $this->est_actif
            && $this->date_debut_contrat <= $now
            && ($this->date_fin_contrat === null || $this->date_fin_contrat >= $now);
    }

    /**
     * Check if contract is fixed-term
     */
    public function isFixedTerm(): bool
    {
        return $this->type_contrat === 'cdd';
    }

    /**
     * Check if contract is permanent
     */
    public function isPermanent(): bool
    {
        return $this->type_contrat === 'cdi';
    }

    /**
     * Check if contract is part-time
     */
    public function isPartTime(): bool
    {
        return $this->temps_travail === 'temps_partiel';
    }

    /**
     * Check if contract is full-time
     */
    public function isFullTime(): bool
    {
        return $this->temps_travail === 'temps_plein';
    }

    /**
     * Get contract duration in days
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->date_fin_contrat) {
            return null; // Permanent contract
        }

        return (int) \Carbon\Carbon::parse($this->date_debut_contrat)
            ->diffInDays(\Carbon\Carbon::parse($this->date_fin_contrat));
    }

    /**
     * Get remaining contract days
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->date_fin_contrat) {
            return null; // Permanent contract
        }

        $now     = now();
        $endDate = \Carbon\Carbon::parse($this->date_fin_contrat);

        if ($endDate < $now) {
            return 0; // Contract expired
        }

        return (int) $now->diffInDays($endDate);
    }

    /**
     * Calculate monthly salary including primes
     */
    public function getMonthlyGrossSalary(): ?\Money\Money
    {
        if (!$this->salaire_base) {
            return null;
        }

        $baseSalary   = $this->salaire_base;
        $primesAmount = 0;

        if ($this->primes) {
            foreach ($this->primes as $prime) {
                if (isset($prime['montant']) && ($prime['type'] ?? '') === 'mensuelle') {
                    $primesAmount += $prime['montant'];
                }
            }
        }

        if ($primesAmount > 0) {
            $primesMoney = new \Money\Money($primesAmount, $baseSalary->getCurrency());

            return $baseSalary->add($primesMoney);
        }

        return $baseSalary;
    }

    /**
     * Add vacation days
     */
    public function addConges(float $days): void
    {
        $this->update([
            'conges_solde' => $this->conges_solde + $days,
        ]);
    }

    /**
     * Use vacation days
     */
    public function useConges(float $days): bool
    {
        if ($this->conges_solde < $days) {
            return false;
        }

        $this->update([
            'conges_solde' => $this->conges_solde - $days,
        ]);

        return true;
    }

    /**
     * Get contract type display name
     */
    public function getTypeContratDisplayAttribute(): string
    {
        return match ($this->type_contrat) {
            'cdi'           => 'CDI (Contrat à Durée Indéterminée)',
            'cdd'           => 'CDD (Contrat à Durée Déterminée)',
            'stage'         => 'Stage',
            'interim'       => 'Intérim',
            'apprentissage' => 'Apprentissage',
            'freelance'     => 'Freelance',
            default         => $this->type_contrat,
        };
    }

    /**
     * Get work time display name
     */
    public function getTempseTravailDisplayAttribute(): string
    {
        return match ($this->temps_travail) {
            'temps_plein'   => 'Temps plein',
            'temps_partiel' => 'Temps partiel',
            'temps_choisi'  => 'Temps choisi',
            default         => $this->temps_travail,
        };
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): mixed
    {
        return \Database\Factories\Employees\ContratFactory::new(); // @phpstan-ignore-line
    }

    protected function casts(): array
    {
        return [
            'salaire_base'                    => AsMoney::class,
            'salaire_horaire'                 => AsMoney::class,
            'primes'                          => 'array',
            ContratSchema::HORAIRES_STANDARDS => 'array',
            'conges_solde'                    => 'decimal:1',
            ContratSchema::DATE_DEBUT         => 'date',
            ContratSchema::DATE_FIN           => 'date',
            'est_actif'                       => 'boolean',
            ContratSchema::CREATED_AT         => 'timestamp',
            ContratSchema::UPDATED_AT         => 'timestamp',
        ];
    }
}
