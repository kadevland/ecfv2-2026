<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;

return new class extends Migration
{
    protected $connection = UserSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $helper = new SchemaHelper(DB::connection(UserSchema::CONNECTION));

        // Créer le schéma auth pour toutes les tables d'authentification
        $helper->createSchemaIfNotExists(UserSchema::SCHEMA);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $helper = new SchemaHelper(DB::connection(UserSchema::CONNECTION));

        // Supprimer le schéma et toutes ses tables
        $helper->dropSchemaIfExists(UserSchema::SCHEMA);
    }
};
