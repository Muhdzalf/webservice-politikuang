<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Carbon\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Laravel\Sanctum\Sanctum;

class PengawasTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_create_pengawas()
    {
        $faker = Faker::create('id_ID');

        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin);

        $pengawasData = [
            'nama' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => '12345678',
            'no_hp' => $faker->numerify('08232013####'),
            'no_spt' => '01/SK/' . $faker->city() . '/V/2023',
            'jabatan' => 'Ketua Pengawas',
            'mulai_tugas' => '2023-01-01',
            'selesai_tugas' => '2023-06-01',
            'role' => 'pengawas',
        ];

        $response = $this->postJson('api/pengawas/create', $pengawasData);

        $response->assertOk()->dump();
    }
}
