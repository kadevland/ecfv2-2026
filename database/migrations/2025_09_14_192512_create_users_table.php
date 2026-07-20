<?php

declare(strict_types=1);

use App\Enums\UserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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

        // Créer le schéma auth s'il n'existe pas

        Schema::create(UserSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(UserSchema::DB_ID)->primary();

            $table->uuid(UserSchema::ID)->unique();

            $table->string(UserSchema::TYPE, 20)
                ->index();
            $table->boolean(UserSchema::IS_ACTIVE)
                ->default(true)
                ->index();
            $table->timestampsTz();

            // Index pour performance
            $table->index(UserSchema::CREATED_AT);
        });

        // Ajout contrainte CHECK avec helper réutilisable (APRÈS création table)
        $helper = new SchemaHelper(DB::connection(UserSchema::CONNECTION));
        $helper->addEnumCheckConstraint(
            UserSchema::FULL_TABLE,
            UserSchema::TYPE,
            UserType::cases(),
            UserSchema::CONSTRAINT_TYPE_CHECK
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserSchema::FULL_TABLE);
    }
};
