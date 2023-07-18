<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\Masyarakat;
use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_masyarakat_success_register()
    {
        $faker = Faker::create('id_ID');
        $nik = $faker->numerify('320506######0002');
        $email = $faker->safeEmail();

        $payload = [
            'nik' => $nik,
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'email' => $email,
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '085156184235',

            // Data Alamat
            'provinsi_id' => 32,
            'kabupaten_kota_id' => 3205,
            'kecamatan_id' => 3205230,
            'desa' => 'Desa Sukaratu',
            'detail_alamat' => 'Kampung Sompok',

            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        $response = $this->postJson('api/user/register', $payload);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_user',
                'nama',
                'email',
                'role',
                'access_token',
                'type',
            ],
        ]);
    }

    // user registrasi tanpa mencantumkan data
    public function test_masyarakat_get_a_validation_error_when_try_to_register_without_nik_data()
    {
        $faker = Faker::create('id_ID');
        $email = $faker->safeEmail();

        $payload = [
            'nik' => '',
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'email' => $email,
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '085156184235',

            // Data Alamat
            'provinsi_id' => 32,
            'kabupaten_kota_id' => 3205,
            'kecamatan_id' => 3205230,
            'desa' => 'Desa Sukaratu',
            'detail_alamat' => 'Kampung Sompok',

            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        $response = $this->postJson('api/user/register', $payload, ['Accept' => 'application/json']);


        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: The nik field is required.',
        ]); //422
    }

    // Test registrasi dengan email yang sudah digunakan
    public function test_masyarakat_get_a_validation_error_when_try_to_register_with_registered_email()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();

        $payload = [
            'nik' => $faker->numerify('320506######0002'),
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'email' => $masyarakat->email,
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '085156184235',
            'provinsi_id' => 32,
            'kabupaten_kota_id' => 3205,
            'kecamatan_id' => 3205230,
            'desa' => 'Desa Sukaratu',
            'detail_alamat' => 'Aula Desa',
            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/user/register', $payload, ['Accept' => 'application/json']);


        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: The email has already been taken.',
        ]); //400
    }

    // test dengan tanpa mengisi kolom email dan password
    public function test_required_field_for_login()
    {
        $response = $this->postJson('api/user/login', []);

        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: The email field is required.',
        ]);
    }

    // test login berhasil
    public function test_user_success_login()
    {
        $payload = User::factory()->has(Masyarakat::factory())->create();

        $response = $this->postJson('api/user/login', [
            'email' => $payload->email,
            'password' => '12345678'
        ]);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'id_user',
                'nama',
                'email',
                'role',
                'access_token',
                'type',
            ],
        ]);
    }

    public function test_user_get_a_bad_request_error_when_login_with_wrong_email()
    {
        $response = $this->postJson('api/user/login', [
            'email' => 'testemail@gmail.com', //registered email is 'muhdzalfikri@gmail.com
            'password' => '12345678'
        ]);
        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: Cek kembali email dan password Anda',
        ]);
    }

    public function test_user_get_a_bad_request_error_when_login_with_wrong_password()
    {
        $response = $this->postJson('api/user/login', [
            'email' => 'muhdzalfikri@gmail.com',
            'password' => '87654321' // wrong password
        ]);
        $response->assertStatus(400)->assertJson([
            'kode' => 400,
            'status' => false,
            'message' => 'Gagal: Cek kembali email dan password Anda',
        ]);
    }

    public function test_user_success_logout()
    {
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $token = $masyarakat->createToken('usertoken')->plainTextToken;

        Sanctum::actingAs($masyarakat);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('api/logout',); //token ada pada header

        $response->assertOk()->assertJson([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Logout Berhasil',
        ]);
    }
}
