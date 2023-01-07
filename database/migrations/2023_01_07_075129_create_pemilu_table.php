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
        Schema::create('pemilu', function (Blueprint $table) {
            $table->id('id_pemilu');
            $table->string('nama');
            $table->date('tanggal_pelaksanaan');

            $table->unsignedBigInteger('jenis_id');
            $table->foreign('jenis_id')->references('id_jenis')->on('jenis_pemilu')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('alamat_id');
            $table->foreign('alamat_id')->references('id_alamat')->on('alamat_pemilu')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('pemilu');
    }
};
