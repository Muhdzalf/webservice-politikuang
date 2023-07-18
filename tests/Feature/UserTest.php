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
            'jabatan' => $faker->randomElement(['Ketua', 'Anggota']),
            'mulai_tugas' => '2023-01-01',
            'selesai_tugas' => '2023-06-01',
            'role' => 'pengawas',
        ];

        $response = $this->postJson('api/user/pengawas', $pengawasData);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_user',
                'nama',
                'email',
                'role',
            ]
        ]);
    }

    public function test_admin_can_create_other_admin()
    {
        $faker = Faker::create('id_ID');

        $admin = User::factory()->administrator()->create();

        Sanctum::actingAs($admin);

        $AdminData = [
            'nama' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => '12345678',
            'no_hp' => $faker->numerify('08232013####'),
        ];

        $response = $this->postJson('api/user/admin', $AdminData);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_user',
                'nama',
                'email',
                'role',
            ]
        ]);
    }

    public function test_authenticated_user_can_get_their_user_data()
    {
        // test with masyarakat
        // $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $pengawas = User::factory()->petugas()->has(Pengawas::factory())->create();


        // Sanctum::actingAs($masyarakat, ['fetchUser']);
        Sanctum::actingAs($pengawas, ['fetchUser']);

        $response = $this->getJson('api/user');

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    'id_user',
                    'nama',
                    'email',
                    'no_hp',
                    'role',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ]
            ]
        );
    }

    public function test_user_can_update_profile_data()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();

        Sanctum::actingAs($masyarakat);

        // Terdapat Perubahaan pada nama dan alamat
        $dataBaru = [
            'nama' => $faker->name() . ' (edited)', // Edited Name
            'email' => $masyarakat->email,
            'no_hp' => $masyarakat->no_hp,
        ];

        $response = $this->putJson(
            'api/user',
            $dataBaru,
            ['Accept' => 'application/json']
        );

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
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

        $response->assertOk()->assertJsonStructure([
                'kode',
                'status',
                'message',
                'data' =>[
                '*' => [
                    'id_user',
                    'nama',
                    'email',
                    'role',
                ]]
            ]);
    }
}
