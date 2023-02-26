<?php

use Database\Seeders\KecamatanSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->char('id_kecamatan', 7)->primary();
            $table->string('nama', 35);

            // foreign key to kabupaten kota id
            $table->char('kabupaten_kota_id', 4);
            $table->foreign('kabupaten_kota_id')->references('id_kabupaten_kota')->on('kabupaten_kota')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => KecamatanSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
};
