<?php

namespace Tests\Feature;

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
        $payload = [
            'nik' => $faker->numerify('320506######0002'),
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'email' => $faker->safeEmail(),
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => 'L',
            'no_hp' => '085156184235',
            'alamat' => 'Kp. Sompok, Banyuresmi, Garut',
            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        $response = $this->postJson('api/user/register', $payload);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'access_token',
            'type',
            'data' => [
                'nama',
                'email',
                'no_hp',
                'role',
                'masyarakat' => [
                    'nik',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'alamat',
                    'pekerjaan',
                ]
            ],
        ]);
    }

    // user registrasi tanpa mencantumkan data
    public function test_masyarakat_get_a_validation_error_when_try_to_register_without_data()
    {

        $payload = [
            'nik' => '',
            'nama' => '',
            'email' => '',
            'password' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => '',
            'no_hp' => '',
            'alamat' => '',
            'pekerjaan' => '',
            'kewarganegaraan' => '',
        ];

        $response = $this->postJson('api/user/register', $payload, ['Accept' => 'application/json']);

        $response->assertUnprocessable()->assertJsonStructure(
            [
                'message',
                'errors' => [
                    'nik',
                    'nama',
                    'email',
                    'password',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'no_hp',
                    'alamat',
                    'pekerjaan',
                    'kewarganegaraan',

                ]
            ]
        );
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
            'alamat' => 'Kp. Sompok, Banyuresmi, Garut',
            'pekerjaan' => 'Mahasiswa',
            'kewarganegaraan' => 'Indonesia',
        ];

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/user/register', $payload, ['Accept' => 'application/json']);


        $response->assertUnprocessable()->assertJson([
            'message' => 'The email has already been taken.',
            'errors' => [
                'email' => ['The email has already been taken.']
            ]
        ]); //422
    }

    // test dengan tanpa mengisi kolom email dan password
    public function test_required_field_for_login()
    {
        $response = $this->postJson('api/user/login', []);

        $response->assertUnprocessable()->assertJson([
            'message' => 'The email field is required. (and 1 more error)',
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]
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
            'access_token',
            'type',
            'data' => [
                'nama',
                'email',
                'no_hp',
                'role',
            ],
        ]);
    }

    public function test_user_get_a_unauthorized_error_when_login_with_wrong_email()
    {
        $response = $this->postJson('api/user/login', [
            'email' => 'dzalfiqrisabani@gmail.com', //registered email is 'example@gmail.com
            'password' => '12345678'
        ]);
        $response->assertUnauthorized()->assertJson([
            'kode' => 401,
            'status' => false,
            'message' => 'Proses login gagal, siahkan cek kembali email dan password Anda'
        ]);
    }

    public function test_user_get_a_unauthorized_error_when_login_with_wrong_password()
    {
        $response = $this->postJson('api/user/login', [
            'email' => 'muhdzalfikri@gmail.com', //registered email is 'example@gmail.com
            'password' => '87654321'
        ]);
        $response->assertUnauthorized()->assertJson([
            'kode' => 401,
            'status' => false,
            'message' => 'Proses login gagal, siahkan cek kembali email dan password Anda'
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
