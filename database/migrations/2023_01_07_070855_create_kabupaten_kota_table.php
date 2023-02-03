<?php

use Database\Seeders\KabupatenSeeder;
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
        Schema::create('kabupaten_kota', function (Blueprint $table) {
            $table->char('id_kabupaten_kota', 4)->primary();
            $table->string('nama', 40);
            $table->char('provinsi_id', 2);
            $table->foreign('provinsi_id')->references('id_provinsi')->on('provinsi')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => KabupatenSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kabupaten_kota');
    }
};
