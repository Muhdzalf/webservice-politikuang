<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

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

        $response->assertStatus(200)->assertJsonStructure([
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

        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors',
        ])->assertJson([
            'message' => 'The nik field is required.',
            'errors' => [
                'nik' => ['The nik field is required.']
            ]
        ]);
    }

    public function test_user_get_a_validation_error_when_register_with_same_email()
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


        $response->assertStatus(422)->assertJsonStructure([
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
            'tanggal_lahir' => '11-12-2000', // Format d-m-Y
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'nomor_tlp' => '082320136961',
            'alamat' => $faker->address(),
            'pekerjaan' => $faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'role' => 'masyarakat',
        ]);


        $response->assertStatus(422)->assertJsonStructure([
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
}
