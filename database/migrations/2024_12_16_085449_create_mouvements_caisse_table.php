<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_caisse', function (Blueprint $table) {
            $table->id('id_mouvement');
            $table->foreignId('caisse_id')->constrained('caisses', 'id_caisse');
            $table->enum('type_mouvement', ['emprunt', 'remboursement', 'retrait', 'pret']);
            $table->decimal('montant', 15, 2);
            $table->string('motif');
            $table->decimal('solde_avant', 15, 2);
            $table->decimal('solde_apres', 15, 2);
            $table->foreignId('user_id')->constrained('users', 'id_util');

            // Nouvelles colonnes pour la gestion des emprunts et remboursements
            $table->unsignedBigInteger('motif_reference')->nullable();
            $table->foreign('motif_reference')
                ->references('id_mouvement')
                ->on('mouvements_caisse')
                ->onDelete('set null');

            $table->decimal('montant_restant', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes(); // Pour la suppression logique
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_caisse');
    }
};
