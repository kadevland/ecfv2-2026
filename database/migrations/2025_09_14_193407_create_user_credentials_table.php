<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;

return new class extends Migration
{
    protected $connection = UserCredentialSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(UserCredentialSchema::FULL_TABLE, function (Blueprint $table) {
            $table->id(UserCredentialSchema::ID)
                ->primary();

            // Dual-key system pour référencer users
            $table->bigInteger(UserCredentialSchema::USER_KEY)
                ->unique();
            $table->uuid(UserCredentialSchema::USER_ID)
                ->unique();

            $table->string(UserCredentialSchema::EMAIL)
                ->unique();
            $table->string(UserCredentialSchema::PASSWORD_HASH);
            $table->timestamp(UserCredentialSchema::EMAIL_VERIFIED_AT)
                ->nullable();
            $table->string(UserCredentialSchema::REMEMBER_TOKEN, 100)
                ->nullable();
            $table->timestampsTz();

            // FK performance vers auth.users.db_id
            $table->foreign(UserCredentialSchema::USER_KEY)
                ->references(UserSchema::PRIMARY_KEY)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            // FK business vers auth.users.id (UUID)
            $table->foreign(UserCredentialSchema::USER_ID)
                ->references(UserSchema::ID)
                ->on(UserSchema::FULL_TABLE)
                ->onDelete('cascade');

            // Index pour performance
            $table->index(UserCredentialSchema::EMAIL);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserCredentialSchema::FULL_TABLE);
    }
};
