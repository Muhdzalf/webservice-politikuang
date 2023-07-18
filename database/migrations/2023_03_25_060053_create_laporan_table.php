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
            $table->string('nomor_laporan')->primary();
            $table->string('judul', 50);
            $table->integer('nominal');
            $table->string('pemberi', 50);
            $table->string('penerima', 50);
            $table->date('tanggal_kejadian');
            $table->string('tempat_kejadian', 150);
            $table->longText('kronologi_kejadian');
            $table->string('bukti', 200);

            // Foreign key to table pemilu
            $table->unsignedBigInteger('pemilu_id')->nullable();
            $table->foreign('pemilu_id')->references('id_pemilu')->on('pemilu')->nullOnDelete()->onUpdate('cascade');

            // foreign key to table masyarakat
            $table->char('nik', 16);
            $table->foreign('nik')->references('nik')->on('masyarakat')->onDelete('cascade')->onUpdate('cascade');

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
