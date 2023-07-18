<?php

namespace Tests\Feature;

use App\Models\Administrator;
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

    public function test_masyarakat_can_get_all_pemilu_data()
    {
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat);

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
                ]
            ]
        ]);
    }

    public function test_admin_can_get_detail_pemilu_by_Id()
    {
        $faker = Faker::create();

        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['details']);

        $jenisPemilu = DB::table('jenis_pemilu')->pluck('id_jenis');
        $jenisPemiluId = $faker->randomElement($jenisPemilu);

        $alamat = Alamat::factory()->create([
            'provinsi_id' => 32,
            'kabupaten_kota_id' => 3205,
            'kecamatan_id' => 3205230,
            'desa' => 'Desa Sukaratu',
            'detail_alamat' => 'Aula Desa',
        ]);

        $alamatId = $alamat->id_alamat;

        $dataPemilu = [
            'nama' => 'Pemilihan Desa Sukaratu',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemiluId,
            'alamat_id' => $alamatId,
            'admin_id' => $admin->id_admin
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

    public function test_admin_success_create_pemilu()
    {
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();
        Sanctum::actingAs($admin);

        // membuat jenis pemilu untuk diambil id jenisnya
        $jenisPemilu = JenisPemilu::factory()->create(['nama' => 'Pemilihan Kepala Desa']);

        $pemiluData = [
            'nama' => 'Pemilihan Kepala Desa Sukaratu',
            'tanggal_pelaksanaan' => '2023-04-01',
            'waktu_pelaksanaan' => '10:00',
            'jenis_id' => $jenisPemilu->id_jenis, // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat // Banyuresmi
            'desa' => 'Desa Sukaratu',
            'detail_alamat' => 'Aula Desa',
        ];

        $response = $this->postJson('api/pemilu', $pemiluData, ['Accept' => 'Application/Json']);

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

        $payload = [
            'nama' => 'Pemilihan Kepala Desa',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Jenis Pemilu
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa ' . $faker->numberBetween(0, 20),
            'detail_alamat' => 'Aula Desa'
        ];

        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/pemilu', $payload);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Gagal: Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini.'
            ]
        );
    }

    public function test_admin_get_a_validation_error_when_create_pemilu_with_tanggal_pelaksanaan_field_on_null()
    {
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['create']);

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id_jenis');

        $dataPemilu = [
            'nama' => 'Pemilihan Desa Sukasenang',
            'tanggal_pelaksanaan' => '',
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Jenis Pemilu
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa ' . $faker->numberBetween(0, 20),
            'detail_alamat' => 'Aula Desa'
        ];

        $response = $this->postJson('api/pemilu', $dataPemilu);

        $response->assertStatus(400)->assertJson(
            [
                'kode' => 400,
                'status' => false,
                'message' => 'Gagal: The tanggal pelaksanaan field is required.',
            ]
        );
    }

    public function test_admin_can_update_nama_pemilu()
    {
        $faker = Faker::create('id_ID');
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['update']);

        // Deklarasi Data Tetap
        $jenisPemilu = DB::table('jenis_pemilu')->pluck('id_jenis');
        $jenisPemiluId = $faker->randomElement($jenisPemilu);

        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();
        $alamatId = $alamat->id_alamat;

        $dataAwal = [
            'nama' => 'Nama Pemilu Desa ',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemiluId, // Pemilihan Kepala Desa
            'alamat_id' => $alamatId,
            'admin_id' => $admin->id_admin
        ];

        //membuat data pemilu baru
        $pemilu = Pemilu::factory()->create($dataAwal);

        $dataBaru = [
            'nama' => 'Nama Pemilu Desa Sukamulya',
            'tanggal_pelaksanaan' => $pemilu->tanggal_pelaksanaan,
            'waktu_pelaksanaan' => $pemilu->waktu_pelaksanaan,
            'jenis_id' => $jenisPemiluId, // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'desa' => 'Desa Sukamulya',
            'detail_alamat' => 'Aula Desa'
        ];


        $response = $this->putJson('api/pemilu/' . $pemilu->id_pemilu, $dataBaru, ['Accept' => 'Application/json']);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nama',
                'tanggal_pelaksanaan',
                'waktu_pelaksanaan',
                'jenis_id',
                'alamat_id'
            ]
        ]);
    }

    public function test_admin_can_delete_pemilu()
    {
        // $this->withExceptionHandling();

        $admin = User::factory()->administrator()->has(Administrator::factory())->create();


        Sanctum::actingAs($admin);

        $pemilu = Pemilu::factory()->create();

        $response = $this->deleteJson('api/pemilu/' . $pemilu->id_pemilu);

        $response->assertOk()->assertJson(
            [
                'kode' => 200,
                'status' => true,
                'message' => 'Data Pemilu Berhasil Dihapus',
            ]
        );
    }
}
