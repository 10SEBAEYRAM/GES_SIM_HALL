<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_caisse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id')->constrained('caisses', 'id_caisse');
            $table->enum('type_mouvement', ['emprunt', 'remboursement', 'retrait']);
            $table->decimal('montant', 15, 2);
            $table->text('motif');
            $table->decimal('solde_avant', 15, 2);
            $table->decimal('solde_apres', 15, 2);
            $table->foreignId('user_id')->constrained('users', 'id_util');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_caisse');
    }
};