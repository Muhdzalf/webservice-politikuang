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
            $table->enum('status', ['menunggu', 'dibuat', 'dikembalikan', 'diproses', 'selesai']);
            $table->text('keterangan')->nullable();

            // foreign key to laporan table
            $table->string('nomor_laporan', 20);
            $table->foreign('nomor_laporan')->references('nomor_laporan')->on('laporan')->onUpdate('cascade')->onDelete('cascade');

            // foreign key to petugas table
            $table->unsignedBigInteger('pengawas_id')->nullable();
            $table->foreign('pengawas_id')->references('id_pengawas')->on('pengawas')->nullOnDelete()->onUpdate('cascade');

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
