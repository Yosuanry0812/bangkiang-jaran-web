<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn('kuota');
        });
    }

    public function down()
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->integer('kuota')->default(0)->after('harga');
        });
    }
};
