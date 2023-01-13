<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_laporan', function (Blueprint $table) {
            $table->id('id_progress');
            $table->enum('status', ['dibuat', 'diupdate', 'dikembalikan', 'diproses', 'selesai']);
            $table->text('keterangan')->nullable();

            $table->string('nomor_laporan', 20);
            $table->foreign('nomor_laporan')->references('nomor_laporan')->on('laporan')->onUpdate('cascade')->onDelete('cascade');

            $table->char('nik', 16);
            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('progress_laporan');
    }
};
