<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

return new class extends Migration
{
    protected $connection = UserProfilSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $helper = new SchemaHelper(DB::connection(UserProfilSchema::CONNECTION));

        // Créer le schéma user pour toutes les tables utilisateurs
        $helper->createSchemaIfNotExists(UserProfilSchema::SCHEMA);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $helper = new SchemaHelper(DB::connection(UserProfilSchema::CONNECTION));

        // Supprimer le schéma et toutes ses tables
        $helper->dropSchemaIfExists(UserProfilSchema::SCHEMA);
    }
};
