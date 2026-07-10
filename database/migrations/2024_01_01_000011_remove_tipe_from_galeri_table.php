<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }

    public function down()
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->enum('tipe', ['foto', 'video'])->default('foto')->after('id_galeri');
        });
    }
};
