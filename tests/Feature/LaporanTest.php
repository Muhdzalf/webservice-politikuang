<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class LaporanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_masyarakat_can_create_laporan()
    {
        $masyarakat = User::factory()->create();
        $pemiluID = DB::table('pemilu')->pluck('id');

        Sanctum::actingAs($masyarakat, ['createLaporan']);
        $faker = Faker::create('id_ID');


        $response = $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $response->assertOk();
    }

    public function test_masyarakat_get_a_validation_error_when_create_laporan_with_pemberi_and_penerima_value_null()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['createLaporan']);

        $pemiluID = DB::table('pemilu')->pluck('id');


        $response = $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => '',
            'penerima' => '',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $response->assertUnprocessable();
    }

    public function test_masyarakat_can_get_their_detail_laporan()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['details']);

        $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $laporan = Laporan::latest()->first();

        $response = $this->getJson('api/laporan/details/' . $laporan->id);

        $response->assertOk();
    }

    public function test_masyarakat_cannot_access_laporan_owned_by_other_masyarakat()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['details']);

        $laporanID = DB::table('laporan')->pluck('id');

        // 2 adalah id laporan milik orang lain
        $response = $this->getJson('api/laporan/details/' . $faker->randomElement($laporanID));

        $response->assertForbidden();
    }

    public function test_masyarakat_can_get_progress_laporan()
    {
        $masyarakat = User::factory()->create();
        $pemiluID = DB::table('pemilu')->pluck('id');

        Sanctum::actingAs($masyarakat, ['getProgressLaporan']);
        $faker = Faker::create('id_ID');


        $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $laporan = Laporan::latest()->first();

        $response = $this->getJson('/laporan/progress/' . $laporan->id);

        $response->assertOk();
    }
    public function test_masyarakat_can_update_their_laporan()
    {
        $masyarakat = User::factory()->create();
        $pemiluID = DB::table('pemilu')->pluck('id');

        Sanctum::actingAs($masyarakat, ['createLaporan', 'updateByUser']);
        $faker = Faker::create('id_ID');


        $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $laporan = Laporan::latest()->first();
        $response = $this->postJson('api/laporan/update/' . $laporan->id, [
            'judul' => 'Judul Telah Diedit',
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $response->assertOk()->dump();
    }

    public function test_masyarakat_can_delete_their_laporan_data()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['delete']);

        $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $laporan = Laporan::latest()->first();
        $response = $this->postJson('api/laporan/delete/' . $laporan->id);

        $response->assertOk();
    }

    // Status: Dibuat, Diupdate, Diproses, Ditolak, Dikembalikan, Selesai
    public function test_petugas_can_update_status_laporan()
    {
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['changeStatus']);

        $laporan = DB::table('laporan')->pluck('id');
        $id = $faker->randomElement($laporan);

        $response = $this->postJson('api/laporan/status/' . $id, [
            'laporan_id' => $id,
            'status' => 'Diproses',
            'keterangan' => 'Laporan Sedang Diproses Oleh Petugas. Proses Maksimal 3x24 jam'
        ]);

        $response->assertOk()->dump();
    }

    public function test_petugas_get_a_validation_error_when_update_status_laporan_with_invalid_status()
    {
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['changeStatus']);

        $laporan = DB::table('laporan')->pluck('id');
        $id = $faker->randomElement($laporan);


        $response = $this->postJson('api/laporan/status/' . $id, [
            'laporan_id' => $id,
            'status' => 'Tidak Bisa',
            'keterangan' => 'Laporan Tidak Bisa Diterima'
        ]);

        $response->assertUnprocessable()->dump();
    }
    public function test_masyarakat_can_get_a_list_of__their_laporan_that_have_been_made()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['getUserLaporan']);

        $this->postJson('api/laporan/create', [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tahun_kejadian' => $faker->year(),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'lokasi_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ]);

        $response = $this->getJson('api/laporan/my-laporan');

        $response->assertOk();
    }

    public function test_petugas_can_get_all_list_laporan_that_have_been_made_by_masyarakat()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['allLaporan']);

        $response = $this->getJson('api/laporan');

        $response->assertOK();
    }
}
