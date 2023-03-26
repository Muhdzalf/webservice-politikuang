<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Fqa;
use App\Models\Masyarakat;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FQATest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_required_field()
    {
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['create']);

        $response = $this->postJson('api/fqa');

        $response->assertUnprocessable()->assertJson(
            [
                "message" => "The pertanyaan field is required. (and 1 more error)",
                "errors" => [
                    "pertanyaan" => ["The pertanyaan field is required."],
                    "jawaban" => ["The jawaban field is required."],
                ]
            ]
        );
    }

    public function test_only_admin_can_create_fqa()
    {
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['create']);

        $response = $this->postJson('api/fqa', [
            'pertanyaan' => 'Bagaimana Membuat Sebuah Laporan',
            'jawaban' => ': Laporan dapat dibuat dengan mengklik buat laporan kemudian mengisi data-data yang dibutuhkan.',
        ]);

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                "data" => [
                    "id_fqa",
                    "pertanyaan",
                    "jawaban",
                    "created_at",
                    "updated_at",
                ]
            ]
        ); //200
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_create_fqa()
    {
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/fqa', [
            'pertanyaan' => 'Bagaimana Cara menggunakan Aplikasi Ini',
            'jawaban' => 'Untuk menggunakan aplikasi ini silahkan klik buat laporan untuk membuat laporan baru',
        ]);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ]
        ); //403
    }

    public function test_admin_get_a_validation_error_when_create_fqa_with_jawaban_value_null()
    {
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['create']);

        $response = $this->postJson('api/fqa', [
            'pertanyaan' => 'Siapa Saja yang dapat membuat laporan?',
            'jawaban' => '',
        ]);

        $response->assertUnprocessable()->assertJson(
            [
                "message" => "The jawaban field is required.",
                "errors" => [
                    "jawaban" => ["The jawaban field is required."],
                ]
            ]
        ); //422
    }

    public function test_admin_can_update_fqa_data()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->administrator()->has(Administrator::factory())->create();

        Sanctum::actingAs($admin, ['update']);

        $fqa = Fqa::factory()->create();

        $payload = [
            'pertanyaan' => 'Siapa saja yang dapat membuat laporan?',
            'jawaban' => 'laporan pelanggaran politik uang dapat dibuat oleh siapa saja',
            'admin_id' => $admin->id_admin
        ];

        $response = $this->putJson('api/fqa/' . $fqa->id_fqa, $payload);

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                "data" => [
                    "id_fqa",
                    "pertanyaan",
                    "jawaban",
                    "created_at",
                    "updated_at",
                ]
            ]
        ); //200
    }

    // public function test_admin_get_a_validation_error_when_try_to_update_fqa_data_with_jawaban_value_null()
    // {
    //     $fqa = Fqa::factory()->create([]);

    // $admin = User::factory()->administrator()->has(Administrator::factory())->create();
    //
    //     Sanctum::actingAs($admin, ['update']);

    //     $response = $this->putJson('api/fqa/' . $fqa->id_fqa, [
    //         'pertanyaan' => 'Hal Apa Saja Yang Termasuk Politik Uang',
    //         'jawaban' => '',
    //     ]);

    //     $response->assertUnprocessable()->assertJson(
    //         [
    //             "message" => "The jawaban field is required.",
    //             "errors" => [
    //                 "jawaban" => ["The jawaban field is required."],
    //             ]
    //         ]
    //     ); //422
    // }

    public function test_admin_success_delete_fqa_data()
    {

        $admin = User::factory()->administrator()->has(Administrator::factory())->create();
        Sanctum::actingAs($admin, ['delete']);

        $fqa = Fqa::factory()->create();

        $response = $this->deleteJson('api/fqa/' . $fqa->id_fqa);

        $response->assertOk()->assertJson([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA berhasil Dihapus',
        ]); //200
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_delete_fqa_data()
    {
        $fqa = Fqa::factory()->create([]);

        $admin = User::factory()->create();
        Sanctum::actingAs($admin, ['delete']);

        $response = $this->deleteJson('api/fqa/' . $fqa->id_fqa);

        $response->assertForbidden()->assertJson([
            'kode' => 403,
            'status' => false,
            'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
        ]); //403
    }

    public function test_user_success_get_all_fqa_data()
    {
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->getJson('api/fqa');

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    '*' => [
                        "id_fqa",
                        "pertanyaan",
                        "jawaban",
                        "created_at",
                        "updated_at",
                    ]
                ]
            ]
        ); //200
    }
}
