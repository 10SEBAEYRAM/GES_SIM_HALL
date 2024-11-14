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
            Schema::table('transactions', function (Blueprint $table) {
                // Ajouter la colonne `frais_service`
                $table->decimal('frais_service', 10, 2)->nullable()->after('commission_appliquee');
            });
        }

        public function down()
        {
            Schema::table('transactions', function (Blueprint $table) {
                // Supprimer la colonne `frais_service`
                $table->dropColumn('frais_service');
            });
        }

};
