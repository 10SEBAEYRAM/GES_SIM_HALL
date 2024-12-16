<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->decimal('emprunt_sim_hall', 15, 2)->default(0)->after('balance_caisse');
            $table->decimal('montant_retrait', 15, 2)->default(0)->after('emprunt_sim_hall');
            $table->decimal('remboursement_sim_hall', 15, 2)->default(0)->after('montant_retrait');
        });
    }

    public function down()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn([
                'emprunt_sim_hall',
                'montant_retrait',
                'remboursement_sim_hall'
            ]);
        });
    }
};