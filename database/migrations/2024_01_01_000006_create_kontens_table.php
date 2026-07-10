<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('konten', function (Blueprint $table) {
            $table->id('id_konten');
            $table->string('judul', 200);
            $table->text('isi');
            $table->string('jenis', 50);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konten');
    }
};
