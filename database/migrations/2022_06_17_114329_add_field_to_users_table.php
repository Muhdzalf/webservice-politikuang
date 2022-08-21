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
        Schema::table('users', function (Blueprint $table) {
            $table->char('nik', 16)->after('id');
            $table->string('alamat', 150)->after('nik');
            $table->string('nomor_tlp', 13)->after('email');
            $table->date('tanggal_lahir')->after('nomor_tlp');
            $table->char('jenis_kelamin', 1)->after('tanggal_lahir');
            $table->string('pekerjaan', 20)->after('jenis_kelamin');
            $table->string('kewarganegaraan', 30)->after('pekerjaan');
            $table->string('role', 15)->default('masyarakat')->after('kewarganegaraan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'alamat', 'tanggal_lahir', 'jenis_kelamin', 'nomor_tlp', 'pekerjaan', 'role', 'kewarganegaraan']);
        });
    }
};
