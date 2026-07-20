<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Profiles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Infrastructure\Database\Schemas\Profiles\ClientProfileSchema;

/**
 * @property int $id
 * @property int $user_db_id
 * @property string $user_uuid
 * @property string $prenom
 * @property string $nom
 * @property string|null $telephone
 * @property \Illuminate\Support\Carbon|null $date_naissance
 * @property array|null $adresse_facturation
 * @property array|null $adresse_livraison
 * @property array|null $preferences_communication
 * @property \Illuminate\Support\Carbon|null $donnees_anonymisees_le
 * @property \Illuminate\Support\Carbon|null $derniere_activite
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class ClientProfile extends Model
{
    use HasUuids;

    protected $connection = ClientProfileSchema::CONNECTION;

    protected $table = ClientProfileSchema::FULL_TABLE;

    protected $primaryKey = ClientProfileSchema::PRIMARY_KEY;

    protected $fillable = [
        ClientProfileSchema::USER_DB_ID,
        ClientProfileSchema::USER_UUID,
        ClientProfileSchema::PRENOM,
        ClientProfileSchema::NOM,
        ClientProfileSchema::TELEPHONE,
        ClientProfileSchema::DATE_NAISSANCE,
        ClientProfileSchema::ADRESSE_FACTURATION,
        ClientProfileSchema::ADRESSE_LIVRAISON,
        ClientProfileSchema::PREFERENCES_COMMUNICATION,
        ClientProfileSchema::DONNEES_ANONYMISEES_LE,
        ClientProfileSchema::DERNIERE_ACTIVITE,
    ];

    protected function casts(): array
    {
        return [
            ClientProfileSchema::DATE_NAISSANCE            => 'date',
            ClientProfileSchema::ADRESSE_FACTURATION       => 'array',
            ClientProfileSchema::ADRESSE_LIVRAISON         => 'array',
            ClientProfileSchema::PREFERENCES_COMMUNICATION => 'array',
            ClientProfileSchema::DONNEES_ANONYMISEES_LE    => 'datetime',
            ClientProfileSchema::DERNIERE_ACTIVITE         => 'datetime',
            ClientProfileSchema::CREATED_AT                => 'datetime',
            ClientProfileSchema::UPDATED_AT                => 'datetime',
        ];
    }
}
