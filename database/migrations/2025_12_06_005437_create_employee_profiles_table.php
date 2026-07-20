<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('pgsql')->create('profiles.employee_profiles', function (Blueprint $table) {
            $table->id(); // PRIMARY KEY
            $table->unsignedBigInteger('user_db_id')->unique(); // FK technique vers users.db_id
            $table->uuid('user_uuid')->unique(); // FK business vers users.id
            $table->string('nom');
            $table->string('prenom');
            $table->string('email_professionnel')->unique()->nullable();
            $table->string('telephone_professionnel')->nullable();
            $table->string('numero_employe')->unique()->nullable();
            $table->date('date_embauche')->nullable();
            $table->string('poste')->nullable();
            $table->string('departement')->nullable();
            $table->uuid('cinema_id')->nullable();
            $table->uuid('responsable_uuid')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_uuid');
            $table->index('user_db_id');
            $table->index(['nom', 'prenom']);
            $table->index('poste');
            $table->index('departement');
            $table->index('cinema_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql')->dropIfExists('profiles.employee_profiles');
    }
};
