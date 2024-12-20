<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('grille_tarifaires', 'commission')) {
            Schema::table('grille_tarifaires', function (Blueprint $table) {
                $table->decimal('commission', 10, 2)->after('montant_max');
            });
        }
    }

    public function down()
    {
        Schema::table('grille_tarifaires', function (Blueprint $table) {
            if (Schema::hasColumn('grille_tarifaires', 'commission')) {
                $table->dropColumn('commission');
            }
        });
    }
};