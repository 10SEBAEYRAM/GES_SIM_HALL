<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('grille_tarifaires', 'type_transaction_id')) {
            $defaultTypeId = DB::table('type_transactions')
                ->first()
                ->id_type_transa ?? null;

            if ($defaultTypeId) {
                DB::table('grille_tarifaires')
                    ->whereNull('type_transaction_id')
                    ->update(['type_transaction_id' => $defaultTypeId]);
            }
        }
    }

    public function down()
    {
        // Pas besoin de down car c'est une migration de données
    }
}; 