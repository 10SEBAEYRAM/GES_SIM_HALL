<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grille_tarifaires', function (Blueprint $table) {
            $table->id('id_grille_tarifaire');
            $table->foreignId('produit_id')->constrained('produits', 'id_prod');
            $table->decimal('montant_min', 15, 2);
            $table->decimal('montant_max', 15, 2);
            $table->decimal('commission_grille_tarifaire', 15, 2);
            $table->date('date_validite');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grille_tarifaires');
    }
};
