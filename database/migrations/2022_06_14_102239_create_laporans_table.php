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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->uuid('nomor_laporan', 8)->unique();
            $table->string('judul', 30);
            $table->time('waktu_kejadian');
            $table->date('tanggal_keajadian');
            $table->string('pemberi', 50);
            $table->string('penerima', 50);
            $table->integer('nominal', 15);
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
        Schema::dropIfExists('laporans');
    }
};
