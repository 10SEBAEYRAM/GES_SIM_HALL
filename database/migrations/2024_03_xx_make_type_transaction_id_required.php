<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('grille_tarifaires', 'type_transaction_id')) {
            Schema::table('grille_tarifaires', function (Blueprint $table) {
                $table->unsignedBigInteger('type_transaction_id')->nullable(false)->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('grille_tarifaires', 'type_transaction_id')) {
            Schema::table('grille_tarifaires', function (Blueprint $table) {
                $table->unsignedBigInteger('type_transaction_id')->nullable()->change();
            });
        }
    }
}; 