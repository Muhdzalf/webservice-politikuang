<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\JenisPemilu;
use App\Models\Laporan;
use App\Models\Pemilu;
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
    public function test_masyarakat_sucess_create_laporan()
    {
        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['createLaporan']);
        $faker = Faker::create('id_ID');

        // membuat jenis pemilu baru
        $jenisPemilu = JenisPemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa'
        ]);

        // Alamat
        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();

        // membuat pemilu baru
        $pemilu = Pemilu::factory()->create([
            'nama' => 'Pemilihan Desa Sukaratu',
            'tanggal_pelaksanaan' => $faker->date(),
            'jenis_id' => $jenisPemilu->id_jenis,
            'alamat_id' => $alamat->id_alamat
        ]);

        $laporanData = [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => $faker->name(),
            'penerima' => $faker->name(),
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $pemilu->id_pemilu,
            'pelapor' => $masyarakat->nik
        ];

        $response = $this->postJson('api/laporan/create', $laporanData, ['Accept' => 'Application/json']);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nomor_laporan',
                'judul',
                'pemberi',
                'penerima',
                'nominal',
                'alamat_kejadian',
                'kronologi_kejadian',
                'tanggal_kejadian',
                'bukti',
                'pemilu_id',
                'pelapor'
            ]
        ]);
    }

    public function test_masyarakat_get_a_validation_error_when_create_laporan_with_pemberi_and_penerima_field_null()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['createLaporan']);

        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $dataLaporan = [
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => '',
            'penerima' => '',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID)
        ];

        $response = $this->postJson('api/laporan/create', $dataLaporan, ['Accept' => 'Application/json']);

        $response->assertUnprocessable()->assertJson(
            [
                "message" => "The pemberi field is required. (and 1 more error)",
                "errors" => [
                    "pemberi" => ["The pemberi field is required."],
                    "penerima" => ["The penerima field is required."],
                ]
            ]
        );
    }

    public function test_masyarakat_can_get_their_detail_laporan()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['details']);

        $laporanData = [
            'nomor_laporan' => $faker->numerify('000-00-00-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'pelapor' => $masyarakat->nik
        ];

        $laporan = Laporan::factory()->create($laporanData);

        $response = $this->getJson('api/laporan/' . $laporan->nomor_laporan);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nomor_laporan',
                'judul',
                'pemberi',
                'penerima',
                'nominal',
                'alamat_kejadian',
                'kronologi_kejadian',
                'tanggal_kejadian',
                'bukti',
                'pemilu_id',
                'pelapor'
            ]
        ]);
    }

    public function test_masyarakat_cannot_access_laporan_owned_by_other_masyarakat()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['details']);

        //mengambil secara acak nomor laporan dari database
        $laporanID = DB::table('laporan')->pluck('nomor_laporan');

        // 2 adalah id laporan milik orang lain
        $response = $this->getJson('api/laporan/' . $faker->randomElement($laporanID));

        $response->assertForbidden()->assertJson(
            [
                "kode" => 403,
                "status" => "Forbidden",
                "message" => "Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini"
            ]
        );
    }

    public function test_masyarakat_can_update_their_laporan()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->create();
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        Sanctum::actingAs($masyarakat, ['createLaporan', 'updateByUser']);

        //data tetap
        $tanggal = $faker->date('Y-m-d');
        $nominal = 100000;
        $alamat = $faker->address();
        $kronologi = $faker->text();
        $bukti = $faker->url();
        $pemiluId = $faker->randomElement($pemiluID);

        $laporanData = [
            'nomor_laporan' => $faker->numerify('000-00-00-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $tanggal,
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => $nominal,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
            'pelapor' => $masyarakat->nik
        ];

        // data baru akan menyertakan perubahan data judul serta nominal
        $dataBaru = [
            'judul' => 'Judul Telah Diedit',
            'tanggal_kejadian' => $tanggal,
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => 100000000,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
        ];

        $laporan = Laporan::factory()->create($laporanData);

        $response = $this->putJson('api/laporan/update/' . $laporan->nomor_laporan, $dataBaru);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nomor_laporan',
                'judul',
                'pemberi',
                'penerima',
                'nominal',
                'alamat_kejadian',
                'kronologi_kejadian',
                'tanggal_kejadian',
                'bukti',
                'pemilu_id',
                'pelapor'
            ]
        ]);
    }

    public function test_masyarakat_can_delete_their_laporan_data()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['delete']);

        $laporanData = [
            'nomor_laporan' => $faker->numerify('000-00-01-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'pelapor' => $masyarakat->nik
        ];

        $laporan = Laporan::factory()->create($laporanData);

        $response = $this->deleteJson('api/laporan/delete/' . $laporan->nomor_laporan);

        $response->assertOk()->assertJson(
            [
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data Laporan ' . $laporan->judul . ' berhasil dihapus'
            ]
        );
    }

    public function test_masyarakat_can_get_a_list_of_created_laporan()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');


        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['getUserLaporan']);

        $laporanData = [
            'nomor_laporan' => $faker->numerify('000-00-01-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'pelapor' => $masyarakat->nik
        ];

        $laporan = Laporan::factory()->create($laporanData);

        $response = $this->getJson('api/user/laporan');

        $response->assertOK()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'nomor_laporan',
                    'judul',
                    'pemberi',
                    'penerima',
                    'nominal',
                    'alamat_kejadian',
                    'kronologi_kejadian',
                    'tanggal_kejadian',
                    'bukti',
                    'pemilu_id',
                    'pelapor'
                ]
            ]
        ]);
    }

    public function test_petugas_can_get_all_list_laporan_that_have_been_made_by_masyarakat()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['getAll']);

        $response = $this->getJson('api/laporan');

        $response->assertOK()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'nomor_laporan',
                    'judul',
                    'pemberi',
                    'penerima',
                    'nominal',
                    'alamat_kejadian',
                    'kronologi_kejadian',
                    'tanggal_kejadian',
                    'bukti',
                    'pemilu_id',
                    'pelapor'
                ]
            ]
        ]);
    }
}
