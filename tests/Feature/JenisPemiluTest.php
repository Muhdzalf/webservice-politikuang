<?php

namespace Tests\Feature;

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
    public function test_required_field_jenis_pemilu()
    {
        $admin = User::factory()->administrator()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('api/jenis-pemilu/create', [], ['Accept' => 'application/json']);

        $response->assertUnprocessable()->assertJson([
            "message" => "The nama field is required.",
            "errors" => [
                "nama" => ["The nama field is required."],
            ]
        ]); //422
    }

    public function test_admin_success_create_jenis_pemilu()
    {
        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin, ['create']);

        $response = $this->postJson('api/jenis-pemilu/create', [
            'nama' => 'Pemilihan Presiden dan Wakil Presiden'
        ], ['Accept' => 'application/json']);

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


    public function test_masyarakat_get_a_forbidden_error_when_try_to_create_jenis_pemilu()
    {
        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/jenis-pemilu/create', [
            'nama' => 'Pemilihan Presiden dan Wakil Presiden'
        ]);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya admin yang memiliki akses untuk fitur ini'
            ]
        ); //403
    }

    public function test_admin_success_update_jenis_pemilu()
    {
        $jenisPemilu = JenisPemilu::factory()->create();

        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin, ['update']);

        $response = $this->putJson('api/jenis-pemilu/update/' . $jenisPemilu->id_jenis, [
            'nama' => 'Pemilihan Presiden dan Wakil Presiden (hasil edit)'
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

    public function test_masyarakat_get_a_forbidden_error_when_try_to_update_jenis_pemilu()
    {
        $jenisPemilu = JenisPemilu::factory()->create();

        $masyarakat = User::factory()->create();

        Sanctum::actingAs($masyarakat, ['update']);

        $response = $this->putJson('api/jenis-pemilu/update/' . $jenisPemilu->id_jenis, [
            'nama' => 'Pemilihan Presiden (PILPRES)'
        ], ['Accept' => 'application/json']);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Hanya admin yang memiliki akses untuk fitur ini'
            ]
        );
    }

    public function test_admin_can_delete_Jenis_pemilu()
    {
        $jenisPemilu = JenisPemilu::factory()->create();

        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin, ['delete']);

        $response = $this->deleteJson('api/jenis-pemilu/delete/' . $jenisPemilu->id_jenis, [], ['Accept' => 'application/json']);

        $response->assertOk()->assertJson([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data Jenis Pemilu Berhasil Dihapus'
        ]);
    }

    public function test_user_can_get_all_data_jenis_pemilu()
    {
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
}
