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
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->char('nik', 16)->primary();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('pekerjaan', 35);
            $table->string('kewarganegaraan', 10)->default('Indonesia');

            //foreign key to alamat table
            $table->unsignedBigInteger('alamat_id')->nullable();
            $table->foreign('alamat_id')->references('id_alamat')->on('alamat')->nullOnDelete()->cascadeOnUpdate();
            //foreign key to user table
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('masyarakat');
    }
};
