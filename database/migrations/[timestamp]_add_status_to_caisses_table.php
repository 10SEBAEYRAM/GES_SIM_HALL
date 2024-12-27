<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('balance_caisse');
        });
    }

    public function down()
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
