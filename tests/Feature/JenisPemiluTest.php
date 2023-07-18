<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\JenisPemilu;
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
    public function test_user_can_get_all_data_jenis_pemilu()
    {
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['delete']);

        $response = $this->getJson('api/jenis-pemilu');

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'id_jenis',
                    'nama',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function test_admin_success_create_jenis_pemilu()
    {
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['create']);

        $payload = ['nama' => 'Pemilihan Kepala Desa Tamantirto'];

        $response = $this->postJson('api/jenis-pemilu', $payload, ['Accept' => 'application/json']);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_jenis',
                'nama',
                'created_at',
                'updated_at'
            ]

        ]); // 200
    }

    public function test_admin_get_a_validation_error_when_try__to_create_without_nama()
    {
        $this->withExceptionHandling();
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin);

        $payload = [
            'nama' => '',
        ];

        $response = $this->postJson('api/jenis-pemilu', $payload, ['Accept' => 'application/json']);

        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: The nama field is required.',
        ]); //422
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_create_jenis_pemilu()
    {
        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['create']);

        $payload = ['nama' => 'Pemilihan Presiden dan Wakil Presiden'];

        $response = $this->postJson('api/jenis-pemilu', $payload);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Gagal: Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ]
        ); //403
    }

    public function test_admin_success_update_jenis_pemilu()
    {
        $jenisPemilu = JenisPemilu::factory()->create();

        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['update']);

        $response = $this->putJson('api/jenis-pemilu/' . $jenisPemilu->id_jenis, [
            'nama' => 'Pemilihan Kepada Desa Tamantirto (hasil edit)'
        ]);

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' =>
                [
                    'id_jenis',
                    'nama',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
    }

    public function test_admin_can_delete_Jenis_pemilu()
    {

        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        $jenisPemilu = JenisPemilu::factory()->create();

        Sanctum::actingAs($admin, ['delete']);

        $response = $this->deleteJson('api/jenis-pemilu/' . $jenisPemilu->id_jenis, [], ['Accept' => 'application/json']);

        $response->assertOk()->assertJson([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Jenis Pemilu Berhasil Dihapus'
        ]);
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_delete_jenis_pemilu()
    {
        $jenisPemilu = JenisPemilu::factory()->create();

        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['delete']);

        $response = $this->deleteJson('api/jenis-pemilu/' . $jenisPemilu->id_jenis, ['Accept' => 'application/json']);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Gagal: Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ]
        );
    }
}
