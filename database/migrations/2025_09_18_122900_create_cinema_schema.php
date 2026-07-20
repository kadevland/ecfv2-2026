<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

return new class extends Migration
{
    protected $connection = CinemaSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $helper = new SchemaHelper(DB::connection(CinemaSchema::CONNECTION));

        // Créer le schéma cinema pour toutes les tables de cinéma
        $helper->createSchemaIfNotExists(CinemaSchema::SCHEMA);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $helper = new SchemaHelper(DB::connection(CinemaSchema::CONNECTION));

        // Supprimer le schéma et toutes ses tables
        $helper->dropSchemaIfExists(CinemaSchema::SCHEMA);
    }
};
