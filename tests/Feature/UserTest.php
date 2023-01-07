<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_user_can_register()
    {

        $faker = Faker::create('id_ID');

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/register', [
            'nama' => $faker->name(),
            'nik' => $faker->numerify('320506##########'),
            'email' => 'example@gmail.com',
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'nomor_tlp' => '082320136961',
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ]);

        $response->assertOk()->assertJsonStructure([
            'message',
            'data' => [
                'nama',
                'nik',
                'email',
                'tanggal_lahir',
                'jenis_kelamin',
                'nomor_tlp',
                'alamat',
                'pekerjaan',
                'kewarganegaraan',
                'role',
                'id',
            ],
            'access_token',
            'type'
        ]);
    }

    public function test_user_get_a_validation_error_when_register_without_nik()
    {
        $faker = Faker::create('id_ID');

        // $this->withoutExceptionHandling();
        // user registrasi tanpa mencantumkan nomor nik
        $response = $this->postJson('api/register', [
            'nama' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'nomor_tlp' => '082320136961',
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ]);

        $response->assertUnprocessable()->assertJsonStructure([
            'message',
            'errors',
        ])->assertJson([
            'message' => 'The nik field is required.',
            'errors' => [
                'nik' => ['The nik field is required.']
            ]
        ]);
    }

    public function test_user_get_a_validation_error_when_register_with_registered_email()
    {
        $faker = Faker::create('id_ID');

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/register', [
            'nama' => $faker->name(),
            'nik' => $faker->numerify('320506##########'),
            'email' => 'example@gmail.com',
            'password' => '12345678',
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'nomor_tlp' => '082320136961',
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ]);


        $response->assertUnprocessable()->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ]
        ])->assertJson([
            'message' => 'The email has already been taken.',
            'errors' => [
                'email' => ['The email has already been taken.']
            ]
        ]);
    }

    public function test_user_get_a_validation_error_when_register_with_different_birth_date_format()
    {
        $faker = Faker::create('id_ID');

        // $this->withoutExceptionHandling();
        $response = $this->postJson('api/register', [
            'nama' => $faker->name(),
            'nik' => $faker->numerify('320506##########'),
            'email' => $faker->unique()->safeEmail(),
            'password' => '12345678',
            'tanggal_lahir' => '11-12-2000', // Format yang banar 2000-12-11
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'nomor_tlp' => '082320136961',
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ]);


        $response->assertUnprocessable()->assertJsonStructure([
            'message',
            'errors' => [
                'tanggal_lahir'
            ]
        ])->assertJson([
            'message' => 'The tanggal lahir does not match the format Y-m-d.',
            'errors' => [
                'tanggal_lahir' => ['The tanggal lahir does not match the format Y-m-d.']
            ]
        ]);
    }

    public function test_user_can_login()
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
                'nomor_tlp',
                'alamat',
                'pekerjaan',
                'kewarganegaraan',
                'role',
                'id',
            ]
        ]);
    }
    public function test_user_get_a_unauthorized_error_when_login_with_unregistered_email()
    {
        $response = $this->postJson('api/login', [
            'email' => 'example01@gmail.com', //registered email is 'example@gmail.com
            'password' => '123456789'
        ]);
        $response->assertUnauthorized()->assertJsonStructure([
            'message',
        ])->assertJson([
            'message' => 'Unauthorized'
        ]);
    }
    public function test_user_get_a_unauthorized_error_when_login_with_wrong_password()
    {
        $response = $this->postJson('api/login', [
            'email' => 'example@gmail.com',
            'password' => '1234567890' // valid password: 123456789
        ]);
        $response->assertUnauthorized()->assertJsonStructure([
            'message',
        ])->assertJson([
            'message' => 'Unauthorized'
        ]);
    }

    public function test_user_can_get_their_user_data()
    {
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['fetchUser']);

        $response = $this->getJson('api/user');

        $response->assertOk();
    }

    // public function test_authenticated_user_can_logout()
    // {

    //     $user = User::factory()->create()->first();

    //     // Acting As User
    //     Sanctum::actingAs(
    //         $user['logout']
    //     );

    //     $response = $this->postJson('api/logout');

    //     $response->assertOk();
    // }
}
