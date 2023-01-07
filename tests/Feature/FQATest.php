<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FQATest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_only_petugas_can_create_fqa()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'pertanyaan 1',
            'jawaban' => 'jawaban 1',
        ]);

        $response->assertOk()->dump(); //200
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_create_fqa()
    {
        $masyarakat = User::factory()->create();
        Sanctum::actingAs($masyarakat, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'pertanyaan 2',
            'jawaban' => 'jawaban 2',
        ]);

        $response->assertForbidden(); //403
    }

    public function test_petugas_get_a_validation_error_when_create_fqa_with_jawaban_value_null()
    {
        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['create']);

        $response = $this->postJson('api/fqa/create', [
            'pertanyaan' => 'pertanyaan 1',
            'jawaban' => '',
        ]);

        $response->assertUnprocessable(); //422
    }

    public function test_petugas_can_update_fqa_data()
    {
        $faker = Faker::create('id_ID');
        $fqaid = DB::table('fqa')->pluck('id');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['update']);

        $response = $this->postJson('api/fqa/update/' . $faker->randomElement($fqaid), [
            'pertanyaan' => 'pertanyaan teredit',
            'jawaban' => 'jawaban satu',
        ]);

        $response->assertOk(); //200
    }

    public function test_petugas_get_a_validation_error_when_try_to_update_fqa_data_with_jawaban_value_null()
    {
        $this->withExceptionHandling();
        $faker = Faker::create('id_ID');
        $fqaid = DB::table('fqa')->pluck('id');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['update']);

        $response = $this->postJson('api/fqa/update/' . $faker->randomElement($fqaid), [
            'pertanyaan' => 'pertanyaan teredit',
            'jawaban' => '',
        ]);

        $response->assertUnprocessable(); //422
    }

    public function test_petugas_can_delete_fqa_data()
    {
        $faker = Faker::create('id_ID');
        $fqaid = DB::table('fqa')->pluck('id');

        $petugas = User::factory()->petugas()->create();
        Sanctum::actingAs($petugas, ['delete']);

        $response = $this->postJson('api/fqa/delete/' . $faker->randomElement($fqaid));

        $response->assertOk(); //200
    }

    public function test_masyarakat_get_a_forbidden_error_when_try_to_delete_fqa_data()
    {
        $faker = Faker::create('id_ID');
        $fqaid = DB::table('fqa')->pluck('id');

        $petugas = User::factory()->create();
        Sanctum::actingAs($petugas, ['delete']);

        $response = $this->postJson('api/fqa/delete/' . $faker->randomElement($fqaid));

        $response->assertForbidden(); //403
    }

    public function test_user_can_get_all_fqa_data()
    {
        $response = $this->getJson('api/fqa');

        $response->assertOk(); //200
    }
}
