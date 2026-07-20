<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Profiles;

use App\Infrastructure\Database\Models\Cinema\Cinema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Profiles\EmployeeProfileSchema;

final class EmployeeProfile extends Model
{
    use HasUuids;

    protected $connection = 'pgsql';

    protected $table = 'profiles.employee_profiles';

    protected $fillable = [
        'user_uuid',
        'nom',
        'prenom',
        'poste',
        'departement',
        'salaire',
        'date_embauche',
        'cinema_id',
    ];

    protected $casts = [
        'salaire'       => 'float',
        'date_embauche' => 'date',
    ];


    /**
     * Get the cinema that owns this salle.
     *
     * @return BelongsTo<Cinema, $this>
     */
    public function cinema () : BelongsTo
    {
        /** @var BelongsTo<Cinema, $this> */
        return $this->belongsTo(Cinema::class, EmployeeProfileSchema::CINEMA_KEY, CinemaSchema::ID);
    }

}
