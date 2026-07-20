<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use App\Domain\Shared\Enums\SexeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

return new class extends Migration
{
    protected $connection = UserProfilSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Le schéma profiles existe déjà, créé par client_profiles migration
        $helper = new SchemaHelper(DB::connection(UserProfilSchema::CONNECTION));
        $helper->createSchemaIfNotExists(UserProfilSchema::SCHEMA);

        Schema::create(UserProfilSchema::FULL_TABLE, function (Blueprint $table) {
            // PK auto-increment pour performances des relations (technique)
            $table->id(UserProfilSchema::PRIMARY_KEY);

            // Domain identifier (business) - pattern DDD correct
            $table->uuid(UserProfilSchema::ID)
                ->unique();

            // Foreign Keys vers User (dual FK pattern)
            $table->bigInteger(UserProfilSchema::USER_KEY)->unsigned();
            $table->uuid(UserProfilSchema::USER_ID);

            // User type (duplicated from users table for CQRS read-side optimization)
            $table->enum(UserProfilSchema::TYPE, ['client', 'employee', 'admin'])
                ->comment('Type utilisateur dupliqué pour optimisation CQRS');

            // Données personnelles de base
            $table->string(UserProfilSchema::PRENOM, 100);
            $table->string(UserProfilSchema::NOM, 100);
            $table->string(UserProfilSchema::EMAIL, 320)
                ->unique();
            $table->string(UserProfilSchema::TELEPHONE, 20)
                ->nullable();

            // Données démographiques
            $table->date(UserProfilSchema::DATE_NAISSANCE)
                ->nullable();
            $table->enum(UserProfilSchema::SEXE, SexeEnum::values())
                ->nullable();

            // Adresse JSONB pour flexibilité
            $table->jsonb(UserProfilSchema::ADRESSE)
                ->nullable()
                ->comment('Adresse complète (rue, ville, code_postal, pays)');

            // Préférences utilisateur JSONB
            $table->jsonb(UserProfilSchema::PREFERENCES)
                ->nullable()
                ->comment('Préférences par namespace (notifications, langue, etc.)');

            // Newsletter et communication
            $table->boolean(UserProfilSchema::NEWSLETTER)
                ->default(false);

            $table->timestampsTz();

            // FK vers auth.users (dual FK pattern)
            $table->foreign(UserProfilSchema::USER_KEY)
                ->references(UserSchema::PRIMARY_KEY)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            $table->foreign(UserProfilSchema::USER_ID)
                ->references(UserSchema::ID)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour recherche UUID business
            $table->index(UserProfilSchema::ID, UserProfilSchema::INDEX_UUID);

            // Indexes pour performance
            $table->index([UserProfilSchema::NOM, UserProfilSchema::PRENOM], UserProfilSchema::INDEX_NOM_PRENOM);
            $table->index(UserProfilSchema::USER_KEY, UserProfilSchema::INDEX_USER);

            // Index composite type + uuid pour optimisation CQRS
            $table->index([UserProfilSchema::TYPE, UserProfilSchema::USER_ID], UserProfilSchema::INDEX_TYPE_UUID);
        });

        // Index GIN sur JSONB pour recherche adresse et préférences
        $helper->addGinIndex(
            UserProfilSchema::FULL_TABLE,
            UserProfilSchema::ADRESSE,
            UserProfilSchema::INDEX_ADRESSE
        );

        // Commentaire sur la table
        $helper->addTableComment(
            UserProfilSchema::FULL_TABLE,
            'Profils utilisateurs generiques - donnees personnelles communes a tous types utilisateurs'
        );

        // Migrate existing data: populate type field from users table
        DB::statement('
            UPDATE ' . UserProfilSchema::FULL_TABLE . ' up
            SET ' . UserProfilSchema::TYPE . ' = (
                SELECT u.type
                FROM ' . UserSchema::FULL_TABLE . ' u
                WHERE u.' . UserSchema::PRIMARY_KEY . ' = up.' . UserProfilSchema::USER_KEY . '
            )
            WHERE ' . UserProfilSchema::TYPE . ' IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserProfilSchema::FULL_TABLE);
    }
};
