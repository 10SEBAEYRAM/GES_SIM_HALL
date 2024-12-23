<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            // Renommer les anciennes colonnes si elles existent
            if (Schema::hasColumn('caisses', 'montant_retrait')) {
                $table->renameColumn('montant_retrait', 'total_retraits');
            }
            if (Schema::hasColumn('caisses', 'montant_emprunt')) {
                $table->renameColumn('montant_emprunt', 'total_emprunts');
            }
            if (Schema::hasColumn('caisses', 'montant_remboursement')) {
                $table->renameColumn('montant_remboursement', 'total_remboursements');
            }
            if (Schema::hasColumn('caisses', 'montant_pret')) {
                $table->renameColumn('montant_pret', 'total_prets');
            }
        });
    }

    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            // Restaurer les noms originaux des colonnes
            if (Schema::hasColumn('caisses', 'total_retraits')) {
                $table->renameColumn('total_retraits', 'montant_retrait');
            }
            if (Schema::hasColumn('caisses', 'total_emprunts')) {
                $table->renameColumn('total_emprunts', 'montant_emprunt');
            }
            if (Schema::hasColumn('caisses', 'total_remboursements')) {
                $table->renameColumn('total_remboursements', 'montant_remboursement');
            }
            if (Schema::hasColumn('caisses', 'total_prets')) {
                $table->renameColumn('total_prets', 'montant_pret');
            }
        });
    }
};
