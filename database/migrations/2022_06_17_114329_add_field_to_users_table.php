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
            $table->date('tanggal_lahir')->after('email');
            $table->char('jenis_kelamin', 1);
            $table->string('nomor_tlp', 13);
            $table->string('pekerjaan', 20);
            $table->string('role', 15)->default('masyarakat');
            $table->string('kewarganegaraan', 20);
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
            $table->dropColumn(['nik', 'tanggal_lahir', 'jenis_kelamin', 'nomor_tlp', 'alamat_id', 'pekerjaan', 'role', 'kewarganegaraan']);
        });
    }
};
