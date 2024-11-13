<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ajout de la colonne 'motif' dans la table 'transactions' avec type 'text' pour stocker plusieurs motifs
            $table->text('motif')->nullable()->after('solde_caisse_apres'); // Utilisez 'text' si vous stockez des valeurs plus longues
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Suppression de la colonne 'motif'
            $table->dropColumn('motif');
        });
    }
};

