<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Alamat;
use App\Models\JenisPemilu;
use App\Models\Masyarakat;
use App\Models\Pemilu;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PemiluTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_success_create_pemilu()
    {
        $this->withExceptionHandling();
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->create();

        // membuat jenis pemilu untuk diambil id jenisnya
        $jenisPemilu = JenisPemilu::factory()->create(['nama' => 'Pemilihan Kepala Desa']);

        $pemiluData = [
            'nama' => 'Pemilihan Kepala Desa Sukaratu',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemilu->id_jenis, // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat // Banyuresmi
            'desa' => 'Desa ' . $faker->numberBetween(0, 20),
        ];


        Sanctum::actingAs($admin);

        $response = $this->postJson('api/pemilu/create', $pemiluData, ['Accept' => 'Application/Json']);

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    'id_pemilu',
                    'nama',
                    'tanggal_pelaksanaan',
                    'waktu_pelaksanaan',
                    'jenis_id',
                    'alamat_id',
                ]
            ]
        );
    }

    public function test_masyarakat_get_forbidden_error_when_try_to_create_pemilu()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id_jenis');

        $dataPemilu = [
            'nama' => 'Pemilihan Kepala Desa',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Jenis Pemilu
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa ' . $faker->numberBetween(0, 20),
        ];

        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/pemilu/create', $dataPemilu);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini. Hanya Admin yang memilik akses'
            ]
        );
    }

    public function test_admin_get_a_validation_error_when_create_pemilu_with_nama_field_null()
    {
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->create();

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id_jenis');

        $dataPemilu = [
            'nama' => null,
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Jenis Pemilu
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa ' . $faker->numberBetween(0, 20),
        ];

        Sanctum::actingAs($admin, ['create']);
        $response = $this->postJson('api/pemilu/create', $dataPemilu);

        $response->assertUnprocessable()->assertJson(
            [
                "message" => "The nama field is required.",
                "errors" => [
                    "nama" => ["The nama field is required."],
                ]
            ]
        );
    }


    public function test_admin_can_update_nama_pemilu()
    {
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->create();

        // Deklarasi Data Tetap
        $jenisPemilu = DB::table('jenis_pemilu')->pluck('id_jenis');
        $jenisPemiluId = $faker->randomElement($jenisPemilu);
        $tanggal_pelaksanaan = $faker->date();
        $waktu_pelaksanaan = $faker->time('H:i');


        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();
        $alamatId = $alamat->id_alamat;

        $dataAwal = [
            'nama' => 'Nama Pemilu Desa ',
            'tanggal_pelaksanaan' => $tanggal_pelaksanaan,
            'waktu_pelaksanaan' => $waktu_pelaksanaan,
            'jenis_id' => $jenisPemiluId, // Pemilihan Kepala Desa
            'alamat_id' => $alamatId,
        ];

        //membuat data pemilu baru
        $pemilu = Pemilu::factory()->create($dataAwal);

        $dataBaru = [
            'nama' => 'Nama Pemilu Desa Sukamulya',
            'tanggal_pelaksanaan' => $tanggal_pelaksanaan,
            'waktu_pelaksanaan' => $waktu_pelaksanaan,
            'jenis_id' => $jenisPemiluId, // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa'
        ];

        Sanctum::actingAs($admin, ['update']);

        $response = $this->putJson('api/pemilu/update/' . $pemilu->id_pemilu, $dataBaru, ['Accept' => 'Application/json']);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nama',
                'tanggal_pelaksanaan',
                'waktu_pelaksanaan',
                'jenis_id',
                'alamat_id',

            ]
        ]);
    }

    public function test_admin_can_get_detail_pemilu()
    {
        $faker = Faker::create();

        $admin = User::factory()->administrator()->create();
        Sanctum::actingAs($admin, ['details']);

        $jenisPemilu = DB::table('jenis_pemilu')->pluck('id_jenis');
        $jenisPemiluId = $faker->randomElement($jenisPemilu);

        $alamat = Alamat::factory()->create([
            'provinsi_id' => 32,
            'kabupaten_kota_id' => 3205,
            'kecamatan_id' => 3205230,
            'desa' => 'Sukaratu'
        ]);

        $alamatId = $alamat->id_alamat;

        $dataPemilu = [
            'nama' => 'Pemilihan Desa Sukaratu',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemiluId,
            'alamat_id' => $alamatId,
        ];

        $pemilu = Pemilu::factory()->create($dataPemilu);

        $response = $this->getJson('api/pemilu/' . $pemilu->id_pemilu);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_pemilu',
                'nama',
                'tanggal_pelaksanaan',
                'waktu_pelaksanaan',
                'jenis_id',
                'alamat_id',
            ]
        ]);
    }

    public function test_admin_can_delete_pemilu()
    {
        $this->withExceptionHandling();
        $faker = Faker::create('id_ID');

        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin);


        $pemilu = Pemilu::factory()->create();

        $response = $this->deleteJson('api/pemilu/delete/' . $pemilu->id_pemilu);

        $response->assertOk()->assertJsonStructure(
            [
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data Pemilu Berhasil Dihapus',
            ]
        );
    }

    public function test_get_all_pemilu_data()
    {
        $response = $this->getJson('api/pemilu');

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'id_pemilu',
                    'nama',
                    'tanggal_pelaksanaan',
                    'waktu_pelaksanaan',
                    'jenis_id',
                    'alamat_id'
                ]
            ]
        ]);
    }
}
