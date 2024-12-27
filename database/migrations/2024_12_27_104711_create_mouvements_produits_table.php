<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mouvements_produits', function (Blueprint $table) {
            $table->id('id_mouvement');
            $table->unsignedBigInteger('produit_id');
            $table->string('type_mouvement'); // CREDIT/DEBIT
            $table->text('description');
            
            
            // Nouveaux champs pour les détails des commissions
            $table->integer('volume_depot')->nullable();
            $table->decimal('valeur_depot', 15, 2)->nullable();
            $table->decimal('commission_depot', 15, 2)->nullable();
            
            $table->integer('volume_retrait')->nullable();
            $table->decimal('valeur_retrait', 15, 2)->nullable();
            $table->decimal('commission_retrait', 15, 2)->nullable();
            
            $table->decimal('montant_ht', 15, 2)->nullable();
            $table->decimal('retenue', 15, 2)->nullable();
            $table->decimal('montant_net', 15, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('produit_id')
                  ->references('id_prod')
                  ->on('produits')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mouvements_produits');
    }
};