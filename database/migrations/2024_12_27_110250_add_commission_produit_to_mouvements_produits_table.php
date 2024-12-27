<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mouvements_produits', function (Blueprint $table) {
            $table->decimal('commission_produit', 15, 2)->after('montant_net');
        });
    }

    public function down()
    {
        Schema::table('mouvements_produits', function (Blueprint $table) {
            $table->dropColumn('commission_produit');
        });
    }
};