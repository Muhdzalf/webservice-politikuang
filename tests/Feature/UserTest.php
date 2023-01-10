<?php

namespace Tests\Feature;

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
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['fetchUser']);

        $response = $this->getJson('api/user');

        $response->assertOk()->assertJsonStructure(
            [
                'message',
                'data' => [
                    'nama',
                    'nik',
                    'email',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'no_hp',
                    'alamat',
                    'pekerjaan',
                    'kewarganegaraan',
                    'role',
                ]
            ]
        );
    }

    public function test_user_can_update_profile_data()
    {
        $dataAwal = [
            'nik' => 3205061198212078, // unique, harus selalu diganti ketika melakukan test
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'email' => 'mdz@example.com', // unique, harus selalu diganti ketika melakukan test
            'password' => Hash::make('12345678'),
            'tanggal_lahir' => '1999-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '083218439312',
            'alamat' => 'Bantul Yogyakarta',
            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ];

        $user = User::factory()->create($dataAwal);

        Sanctum::actingAs($user);

        // Terdapat Perubahaan pada nama dan alamat
        $dataBaru = [
            'nama' => 'Sabani Muhammad Z',
            'email' => 'mdz@example.com',
            'tanggal_lahir' => '1999-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '083218439312',
            'alamat' => 'Bandung, Jawa Barat',
            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        $response = $this->putJson('api/user/update', $dataBaru, ['Accept' => 'application/json']);

        $response->assertOk()->assertJsonStructure([
            'message',
            'data' => [
                'nama',
                'nik',
                'email',
                'tanggal_lahir',
                'jenis_kelamin',
                'no_hp',
                'alamat',
                'pekerjaan',
                'kewarganegaraan',
                'role',
            ]
        ]);
    }
}
