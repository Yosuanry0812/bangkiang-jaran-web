<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tiket', function (Blueprint $table) {
            $table->id('id_tiket');
            $table->string('nama_tiket', 100);
            $table->decimal('harga', 10, 2);
            $table->integer('kuota');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tiket');
    }
};
