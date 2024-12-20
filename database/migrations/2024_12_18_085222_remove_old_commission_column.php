<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('grille_tarifaires', function (Blueprint $table) {
            if (Schema::hasColumn('grille_tarifaires', 'commission')) {
                $table->dropColumn('commission');
            }
        });
    }

    public function down()
    {
        // Pas besoin de restaurer l'ancienne colonne
    }
};