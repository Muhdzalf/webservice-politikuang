<?php

namespace Tests\Feature;

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
    public function test_only_petugas_can_create_pemilu()
    {
        $faker = Faker::create('id_ID');
        $petugas = User::factory()->petugas()->create();

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        Sanctum::actingAs($petugas, ['create']);
        $this->withoutExceptionHandling();
        $response = $this->postJson('api/pemilu/create', [
            'nama' => 'Pemilihan Kepala Desa ' . $faker->randomDigit(),
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat // Banyuresmi
            'detail_alamat' => 'Desa ' . $faker->numberBetween(0, 20),
        ]);

        $response->assertOk();
    }
    public function test_masyarakat_get_forbidden_error_when_try_to_create_pemilu()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->create();

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        $dataPemilu = [
            'nama' => 'Pemilihan Kepala Desa',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'detail_alamat' => 'Desa ' . $faker->numberBetween(0, 20),
        ];

        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/pemilu/create', $dataPemilu);

        $response->assertForbidden();
    }

    public function test_petugas_get_a_validation_error_when_create_pemilu_with_null_nama()
    {
        $faker = Faker::create('id_ID');
        $petugas = User::factory()->petugas()->create();

        //Jenis Pemilu ID
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        Sanctum::actingAs($petugas, ['create']);
        $response = $this->postJson('api/pemilu/create', [
            'nama' => null,
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat // Banyuresmi
            'detail_alamat' => 'Desa ' . $faker->numberBetween(0, 20),
        ]);

        $response->assertUnprocessable()->dump();
    }
    public function test_petugas_can_update_nama_pemilu()
    {
        $faker = Faker::create('id_ID');
        $petugas = User::factory()->petugas()->create();

        // getID Pemilu
        $pemiluID = DB::table('pemilu')->pluck('id');
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        $dataPemilu = [
            'nama' => 'Nama Pemilu Telah diedit',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Kepala Desa
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Banyuresmi
            'detail_alamat' => 'Desa' . $faker->numberBetween(0, 20),
        ];

        Sanctum::actingAs($petugas, ['updatePemilu']);

        $response = $this->postJson('api/pemilu/update/' . $faker->randomElement($pemiluID), $dataPemilu);

        $response->assertOk()->assertJsonStructure([
            'message',
            'data'
        ]);
    }

    public function test_petugas_can_get_detail_pemilu()
    {
        $this->withExceptionHandling();
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['details']);

        // getID Pemilu
        $pemiluID = DB::table('pemilu')->pluck('id');

        $response = $this->getJson('api/pemilu/detail/' . $faker->randomElement($pemiluID));

        $response->assertOk()->dump();
    }
    public function test_petugas_can_delete_pemilu()
    {
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['deletePrmilu']);

        // getID Pemilu
        $pemiluID = DB::table('pemilu')->pluck('id');

        $response = $this->postJson('api/pemilu/delete/' . $faker->randomElement($pemiluID));

        $response->assertOk();
    }


    public function test_get_all_pemilu_data()
    {
        $response = $this->getJson('api/pemilu');

        $response->assertOk()->assertJsonStructure([
            'message',
            'data'
        ]);
    }
}
