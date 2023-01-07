<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;


class JenisPemiluTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_only_petugas_can_create_jenis_pemilu()
    {
        $petugas = User::factory()->petugas()->create();

        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/jenis-pemilu/create', [
            'nama' => 'Pemilihan Presiden dan Wakil Presiden'
        ]);

        $response->assertOk();
    }


    public function test_masyarakat_get_a_forbidden_error_when_try_to_create_jenis_pemilu()
    {
        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/jenis-pemilu/create', [
            'nama' => 'Pemilihan Presiden dan Wakil Presiden'
        ]);

        $response->assertForbidden();
    }

    public function test_petugas_get_a_validation_error_when_try_to_create_jenis_pemilu_with_nama_value_null()
    {
        $petugas = User::factory()->petugas()->create();

        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/jenis-pemilu/create', [
            'nama' => '',
        ]);

        $response->assertUnprocessable();
    }

    public function test_petugas_can_update_jenis_pemilu()
    {
        $faker = Faker::create('id_ID');

        $petugas = User::factory()->petugas()->create();

        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        Sanctum::actingAs($petugas, ['update']);

        $response = $this->postJson('api/jenis-pemilu/update/' . $faker->randomElement($jenisPemiluID), [
            'nama' => 'Pemilihan Presiden (PILPRES)'
        ]);

        $response->assertOk()->dump();
    }
    public function test_masyarakat_get_a_forbidden_error_when_try_to_update_jenis_pemilu()
    {
        $faker = Faker::create('id_ID');
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['update']);

        $response = $this->postJson('api/jenis-pemilu/update/' . $faker->randomElement($jenisPemiluID), [
            'nama' => 'Pemilihan Presiden (PILPRES)'
        ]);

        $response->assertForbidden();
    }

    public function test_petugas_can_delete_Jenis_pemilu()
    {
        $faker = Faker::create('id_ID');
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        $petugas = User::factory()->petugas()->create();

        Sanctum::actingAs($petugas, ['delete']);

        $response = $this->postJson('api/jenis-pemilu/delete/' . $faker->randomElement($jenisPemiluID),);

        $response->assertOk();
    }

    public function test_user_can_get_all_data_jenis_pemilu()
    {
        $response = $this->getJson('api/jenis-pemilu');

        $response->assertOk();
    }
}
