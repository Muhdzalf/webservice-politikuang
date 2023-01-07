<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('users', function (Blueprint $table) {
            $table->char('nik', 16)->primary();
            $table->string('nama', 50);
            $table->string('email', 35)->unique();
            $table->date('tanggal_lahir');
            $table->char('jenis_kelamin', 1);
            $table->string('no_hp', 13);
            $table->string('alamat', 150);
            $table->string('pekerjaan', 35);
            $table->string('kewarganegaraan', 10)->default('Indonesia');
            $table->string('role', 10)->set('administrator', 'pengawas', 'masyarakat')->default('masyarakat');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
