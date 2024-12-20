<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('grille_tarifaires', 'type_transaction_id')) {
            Schema::table('grille_tarifaires', function (Blueprint $table) {
                $table->unsignedBigInteger('type_transaction_id')->nullable()->after('id_grille_tarifaire');
            });
        }

        // Ajouter la clé étrangère dans une transaction séparée
        try {
            Schema::table('grille_tarifaires', function (Blueprint $table) {
                $table->foreign('type_transaction_id')
                    ->references('id_type_transa')
                    ->on('type_transactions')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // La clé étrangère existe probablement déjà
        }
    }

    public function down()
    {
        Schema::table('grille_tarifaires', function (Blueprint $table) {
            try {
                $table->dropForeign(['type_transaction_id']);
            } catch (\Exception $e) {
                // La clé étrangère n'existe peut-être pas
            }

            if (Schema::hasColumn('grille_tarifaires', 'type_transaction_id')) {
                $table->dropColumn('type_transaction_id');
            }
        });
    }
}; 