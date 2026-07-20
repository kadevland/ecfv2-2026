<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\DatabaseSchemas;

return new class extends Migration
{
    protected $connection = 'pgsql';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $helper = new SchemaHelper(DB::connection('pgsql'));

        // Créer le schéma reservations pour toutes les tables de réservations
        $helper->createSchemaIfNotExists(DatabaseSchemas::RESERVATIONS);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $helper = new SchemaHelper(DB::connection('pgsql'));

        // Supprimer le schéma et toutes ses tables
        $helper->dropSchemaIfExists(DatabaseSchemas::RESERVATIONS);
    }
};
