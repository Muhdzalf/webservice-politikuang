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
            $table->id();
            $table->string('nomor_laporan', 15);
            $table->string('judul', 50);
            $table->year('waktu_kejadian');
            $table->date('tanggal_keajadian');
            $table->string('pemberi', 50);
            $table->string('penerima', 50);
            $table->bigInteger('nominal');
            $table->string('lokasi_kejadian', 50);
            $table->longText('kronologi_kejadian');
            $table->string('bukti', 200);
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
