<?php

declare(strict_types=1);

use App\Enums\TokenType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Helpers\SchemaHelper;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Auth\UserAccessTokenSchema;

return new class extends Migration
{
    protected $connection = UserAccessTokenSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create(UserAccessTokenSchema::FULL_TABLE, function (Blueprint $table) {
            $table->id(UserAccessTokenSchema::ID)
                ->primary();

            // Dual-key system pour référencer users
            $table->bigInteger(UserAccessTokenSchema::USER_KEY)
                ->index();
            $table->uuid(UserAccessTokenSchema::USER_ID)
                ->index();

            $table->string(UserAccessTokenSchema::TOKEN_TYPE, 50);
            $table->string(UserAccessTokenSchema::TOKEN, 64)
                ->unique();
            $table->timestampTz(UserAccessTokenSchema::LAST_USED_AT)
                ->nullable();
            $table->timestampTz(UserAccessTokenSchema::EXPIRES_AT)
                ->nullable();
            $table->text(UserAccessTokenSchema::NAME)
                ->nullable();
            $table->timestampsTz();

            // Note: Contrainte CHECK ajoutée après création via SchemaHelper

            // FK performance vers auth.users.db_id
            $table->foreign(UserAccessTokenSchema::USER_KEY)
                ->references(UserSchema::DB_ID)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK business vers auth.users.id (UUID)
            $table->foreign(UserAccessTokenSchema::USER_ID)
                ->references(UserSchema::ID)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour performance
            $table->index(UserAccessTokenSchema::EXPIRES_AT);
        });

        // Ajout contrainte CHECK avec helper réutilisable
        $helper = new SchemaHelper(DB::connection(UserAccessTokenSchema::CONNECTION));
        $helper->addEnumCheckConstraint(
            UserAccessTokenSchema::FULL_TABLE,
            UserAccessTokenSchema::TOKEN_TYPE,
            TokenType::cases(),
            UserAccessTokenSchema::CONSTRAINT_TOKEN_TYPE_CHECK
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserAccessTokenSchema::FULL_TABLE);
    }
};
