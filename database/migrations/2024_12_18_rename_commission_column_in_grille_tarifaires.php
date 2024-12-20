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
                $table->renameColumn('commission', 'commission_grille_tarifaire');
            } else if (!Schema::hasColumn('grille_tarifaires', 'commission_grille_tarifaire')) {
                $table->decimal('commission_grille_tarifaire', 10, 2)->after('montant_max');
            }
        });
    }

    public function down()
    {
        Schema::table('grille_tarifaires', function (Blueprint $table) {
            if (Schema::hasColumn('grille_tarifaires', 'commission_grille_tarifaire')) {
                $table->renameColumn('commission_grille_tarifaire', 'commission');
            }
        });
    }
}; 