<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            // Ajout des nouvelles colonnes si elles n'existent pas déjà
            if (!Schema::hasColumn('caisses', 'total_emprunts')) {
                $table->decimal('total_emprunts', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('caisses', 'total_retraits')) {
                $table->decimal('total_retraits', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('caisses', 'total_remboursements')) {
                $table->decimal('total_remboursements', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('caisses', 'total_prets')) {
                $table->decimal('total_prets', 15, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn([
                'total_emprunts',
                'total_retraits',
                'total_remboursements',
                'total_prets'
            ]);
        });
    }
};