<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id_transaction');
            $table->foreignId('type_transaction_id')->constrained('type_transactions', 'id_type_transa');
            $table->foreignId('produit_id')->constrained('produits', 'id_prod');
            $table->foreignId('user_id')->constrained('users', 'id_util');
            $table->decimal('montant_trans', 15, 2);
            $table->decimal('commission_appliquee', 15, 2);
            $table->string('num_beneficiaire');
            $table->enum('type_operation', ['DEPOT', 'RETRAIT']);
            $table->enum('statut', ['EN_COURS', 'COMPLETE', 'ANNULE']);
            $table->decimal('solde_avant', 15, 2);
            $table->decimal('solde_apres', 15, 2);
            $table->decimal('solde_caisse_avant', 15, 2);
            $table->decimal('solde_caisse_apres', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};