<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            // Ajout des nouvelles colonnes si elles n'existent pas
            if (!Schema::hasColumn('caisses', 'emprunt_sim_hall')) {
                $table->decimal('emprunt_sim_hall', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('caisses', 'remboursement_sim_hall')) {
                $table->decimal('remboursement_sim_hall', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('caisses', 'montant_retrait')) {
                $table->decimal('montant_retrait', 15, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            // Suppression des colonnes seulement si elles existent
            $columns = ['emprunt_sim_hall', 'montant_retrait', 'remboursement_sim_hall'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('caisses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
