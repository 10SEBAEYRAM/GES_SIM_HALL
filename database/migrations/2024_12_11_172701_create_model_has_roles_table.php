<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('role_id');
                $table->string('model_type', 191)->default('App\Models\User');
                $table->unsignedBigInteger('model_id');
                $table->timestamps();

                // Contrainte d'unicité
                $table->unique(['role_id', 'model_type', 'model_id'], 'model_has_roles_unique');

                // Index supplémentaires
                $table->index(['role_id', 'model_id', 'model_type']);

                // Clé étrangère
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('model_has_roles');
    }
};
