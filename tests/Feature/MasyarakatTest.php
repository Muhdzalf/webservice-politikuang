<?php

namespace Tests\Feature;

use App\Models\Masyarakat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Laravel\Sanctum\Sanctum;

class MasyarakatTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_masyarakat_can_register()
    {
        $faker = Faker::create('id_ID');

        $masyarakatData = [
            'nik' => $faker->numerify('320506##########'),
            'nama' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'no_hp' => $faker->numerify('08232013####'),
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ];

        $response = $this->postJson('api/registrasi', $masyarakatData);

        $response->assertOk()->dump();
    }

    public function test_masyarakat_can_get_profile()
    {
        // test with masyarakat
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();

        Sanctum::actingAs($masyarakat);

        $response = $this->getJson('api/user/profile');

        $response->assertOk()->dump();
    }
}
