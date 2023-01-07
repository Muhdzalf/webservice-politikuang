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
        Schema::create('laporan', function (Blueprint $table) {
            $table->string('nomor_laporan')->unsigned()->primary();
            $table->string('judul', 50);
            $table->integer('nominal', 15);
            $table->string('pemberi', 50);
            $table->string('penerima', 50);
            $table->date('tanggal_kejadian');
            $table->string('alamat_kejadian', 150);
            $table->longText('kronologi_kejadian');
            $table->string('bukti', 200);

            $table->unsignedBigInteger('pemilu_id')->nullable();
            $table->foreign('pemilu_id')->references('id_pemilu')->on('pemilu')->onUpdate('cascade')->nullOnDelete();

            $table->unsignedBigInteger('pelapor');
            $table->foreign('pelapor')->references('nik')->on('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('laporan');
    }
};
