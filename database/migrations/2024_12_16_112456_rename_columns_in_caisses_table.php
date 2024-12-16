<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->renameColumn('emprunt_sim_hall', 'total_emprunts');
            $table->renameColumn('remboursement_sim_hall', 'total_remboursements');
            $table->renameColumn('montant_retrait', 'total_retraits');
        });
    }

    public function down()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->renameColumn('total_emprunts', 'emprunt_sim_hall');
            $table->renameColumn('total_remboursements', 'remboursement_sim_hall');
            $table->renameColumn('total_retraits', 'montant_retrait');
        });
    }
};