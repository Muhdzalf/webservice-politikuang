<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;
use Laravel\Sanctum\Sanctum;


class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_success_register()
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

        $response = $this->postJson('api/register', $masyarakatData);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'access_token',
            'type',
            'data' => [
                'nik',
                'nama',
                'email',
                'tanggal_lahir',
                'jenis_kelamin',
                'no_hp',
                'alamat',
                'pekerjaan',
                'kewarganegaraan',
                'role',
            ],
            'access_token',
            'type'
        ]);
    }

    // user registrasi tanpa mencantumkan email (data tidak lengkap)
    public function test_user_get_a_validation_error_when_register_without_email()
    {
        $faker = Faker::create('id_ID');
        $masyarakatData = [
            'nik' => $faker->numerify('320506##########'),
            'nama' => $faker->name(),
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'no_hp' => $faker->numerify('08232013####'),
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ];

        $response = $this->postJson('api/register', $masyarakatData, ['Accept' => 'application/json']);

        $response->assertUnprocessable()->assertJson([
            'message' => 'The email field is required.',
            'errors' => [
                'email' => ['The email field is required.']
            ]
        ]); //422
    }

    public function test_user_get_a_validation_error_when_register_with_registered_email()
    {
        $faker = Faker::create('id_ID');

        // membuat user dengan email example@gmail.com
        User::factory()->create(['email' => 'example@gmail.com']);

        $newData = [
            'nik' => $faker->numerify('320506##########'),
            'nama' => $faker->name(),
            'email' => 'example@gmail.com',
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'no_hp' => $faker->numerify('08232013####'),
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ];

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/register', $newData);


        $response->assertUnprocessable()->assertJson([
            'message' => 'The email has already been taken.',
            'errors' => [
                'email' => ['The email has already been taken.']
            ]
        ]); //422
    }

    public function test_required_field_for_login()
    {
        $response = $this->postJson('api/login', []);

        $response->assertUnprocessable()->assertJson([
            'message' => 'The email field is required. (and 1 more error)',
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]
        ]);
    }

    public function test_user_success_login()
    {
        $response = $this->postJson('api/login', [
            'email' => 'example@gmail.com',
            'password' => '12345678'
        ]);

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

    public function test_user_get_a_unauthorized_error_when_login_with_wrong_email_or_password()
    {
        $response = $this->postJson('api/login', [
            'email' => 'example01@gmail.com', //registered email is 'example@gmail.com
            'password' => '123456789'
        ]);
        $response->assertUnauthorized()->assertJson([
            'kode' => 401,
            'status' => 'Unauthorized',
            'message' => 'Proses login gagal, siahkan cek kembali email dan password Anda'
        ]);
    }

    // public function test_user_success_logout()
    // {
    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user['logout']);

    //     $token = $user->tokens();

    //     $response = $this->postJson('api/logout', [], ['Authorization' => $token]); //token ada pada header

    //     $response->assertOk()->assertJson([
    //         'kode' => 200,
    //         'status' => 'OK',
    //         'message' => 'Logout Berhasil',
    //     ]);
    // }
}
