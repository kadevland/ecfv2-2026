<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Infrastructure\Database\Schemas\Auth\PasswordResetTokenSchema;

return new class extends Migration
{
    protected $connection = PasswordResetTokenSchema::CONNECTION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(PasswordResetTokenSchema::FULL_TABLE, function (Blueprint $table) {
            $table->string(PasswordResetTokenSchema::EMAIL)
                ->primary();
            $table->string(PasswordResetTokenSchema::TOKEN);
            $table->timestamp(PasswordResetTokenSchema::CREATED_AT)
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(PasswordResetTokenSchema::FULL_TABLE);
    }
};
