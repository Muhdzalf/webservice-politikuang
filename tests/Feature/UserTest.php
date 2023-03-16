<?php

namespace Tests\Feature;

use App\Models\Masyarakat;
use App\Models\Pengawas;
use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_authenticated_user_can_get_their_user_data()
    {
        // test with masyarakat
        // $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $pengawas = User::factory()->petugas()->has(Pengawas::factory())->create();


        // Sanctum::actingAs($masyarakat, ['fetchUser']);
        Sanctum::actingAs($pengawas, ['fetchUser']);

        $response = $this->getJson('api/user');

        $response->assertOk()->dump();
    }

    public function test_user_can_update_profile_data()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();

        Sanctum::actingAs($masyarakat);

        dump($masyarakat);

        // Terdapat Perubahaan pada nama dan alamat
        $dataBaru = [
            'nama' => 'Nama Telah Diedit',
            'email' => 'edditedemail@mail.com',
            'no_hp' => $masyarakat->no_hp,
        ];

        $response = $this->postJson(
            'api/user/update',
            $dataBaru,
            ['Accept' => 'application/json']
        );

        $response->assertOk()->assertJsonStructure([
            'message',
            'data',
        ])->dump();
    }

    public function test_admin_can_get_all_user()
    {
        // test with masyarakat
        $administrator = User::factory()->administrator()->create();

        Sanctum::actingAs($administrator);

        $response = $this->getJson('api/user/all');

        $response->assertOk()->dump();
    }
}
