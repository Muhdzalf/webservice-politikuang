<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Models\Masyarakat;
use App\Models\Pengawas;
use App\Models\ProgressLaporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProgressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // Status masy: dibuat, dirubah, dihapus,
    // Status pengawas: diproses, ditolak, dikembalikan, Selesai

    // untuk menguji bahwa pengawas dapat merespon laporan yang telah dibuat masyarakat
    public function test_pengawas_success_respon_laporan()
    {
        $this->withExceptionHandling();
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->has(Pengawas::factory())->create();
        Sanctum::actingAs($petugas);

        // mengambil nomor laporan secara acak dari database
        $laporan = DB::table('laporan')->pluck('nomor_laporan');
        $id = $faker->randomElement($laporan);

        $response = $this->postJson('api/laporan/respon/' . $id, [
            'nomor_laporan' => $id,
            'status' => 'diproses',
            'keterangan' => 'Laporan Sedang Diproses Oleh Petugas'
        ]);

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    'nomor_laporan',
                    'pengawas_id',
                    'status',
                    'keterangan'
                ]
            ]
        );
    }

    public function test_petugas_get_a_validation_error_when_update_status_laporan_with_invalid_status()
    {
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['changeStatus']);

        $laporan = DB::table('laporan')->pluck('nomor_laporan');
        $id = $faker->randomElement($laporan);


        $response = $this->postJson('api/laporan/respon/' . $id, [
            'nomor_laporan' => $id,
            'status' => 'Tidak Diterima',
            'keterangan' => 'Laporan Tidak Bisa Diterima karena tidak adanya data pendukung'
        ]);

        $response->assertUnprocessable()->assertJsonStructure([
            [
                'message',
                'errors'
            ]
        ]);
    }

    public function test_masyarakat_can_get_progress_laporan()
    {
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $petugas = User::factory()->petugas()->has(Pengawas::factory())->create();
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        Sanctum::actingAs($masyarakat, ['getProgressLaporan']);
        $faker = Faker::create('id_ID');

        //get user nik
        $data = Masyarakat::where('user_id', $masyarakat->id)->first();
        $usernik = $data->nik;

        $dataLaporan = [
            'nomor_laporan' => $faker->numerify('000-00-01-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => '',
            'penerima' => '',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $usernik
        ];

        $laporan = Laporan::factory()->create($dataLaporan);

        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'diproses',
            'keterangan' => 'laporan telah dibuat oleh ' . $masyarakat->nama,
        ]);

        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'menunggu',
            'keterangan' => 'laporan sedang diproses, silahkan menunggu informasi dari kami',
            // 'pengawas_id' => $petugas->id
        ]);

        $response = $this->getJson('api/laporan/' . $laporan->nomor_laporan . '/progress');
        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    '*' => [
                        'nomor_laporan',
                        'status',
                        'keterangan',
                        'pengawas_id'
                    ]
                ]
            ]
        )->dump();
    }
}
