<?php

namespace Tests\Feature;

use App\Models\Fqa;
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
    public function test_required_field_for_registration()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/fqa/create');

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

    public function test_only_petugas_can_create_fqa()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'Test Contoh Pertanyaan',
            'jawaban' => 'Test Contoh Jawaban',
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
        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'Test pertanyaan',
            'jawaban' => 'Test jawaban',
        ]);

        $response->assertForbidden()->assertJson(
            [
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya petugas yang memiliki akses untuk fitur ini'
            ]
        ); //403
    }

    public function test_petugas_get_a_validation_error_when_create_fqa_with_jawaban_value_null()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'pertanyaan 1',
            'jawaban' => null,
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

    public function test_petugas_can_update_fqa_data()
    {

        $fqa = Fqa::factory()->create();

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['update']);

        $response = $this->putJson('api/fqa/update/' . $fqa->id_fqa, [
            'pertanyaan' => 'pertanyaan telah diedit',
            'jawaban' => 'contoh jawaban',
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

    public function test_petugas_get_a_validation_error_when_try_to_update_fqa_data_with_jawaban_value_null()
    {
        $fqa = Fqa::factory()->create([]);

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['update']);

        $response = $this->putJson('api/fqa/update/' . $fqa->id_fqa, [
            'pertanyaan' => 'Contoh pertanyaan teredit',
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

    public function test_petugas_success_delete_fqa_data()
    {
        $fqa = Fqa::factory()->create([]);

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['delete']);

        $response = $this->deleteJson('api/fqa/delete/' . $fqa->id_fqa);

        $response->assertOk()->assertJson([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA berhasil Dihapus',
        ]); //200
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_delete_fqa_data()
    {
        $fqa = Fqa::factory()->create([]);

        $petugas = User::factory()->create();
        Sanctum::actingAs($petugas, ['delete']);

        $response = $this->deleteJson('api/fqa/delete/' . $fqa->id_fqa);

        $response->assertForbidden()->assertJson([
            'kode' => 403,
            'status' => 'Forbidden',
            'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya petugas yang memiliki akses untuk fitur ini'
        ]); //403
    }

    public function test_user_success_get_all_fqa_data()
    {
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
