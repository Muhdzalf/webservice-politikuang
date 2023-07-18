<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;


class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_get_all_provinsi_data()
    {
        $faker = Faker::create('id_ID');
        $userIds = User::pluck('id_user');
        $user = User::find($faker->randomElement($userIds));

        Sanctum::actingAs($user);
        $response = $this->getJson('api/provinsi');

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_provinsi',
                        'nama'
                    ]
                ]
            ]
        );
    }

    public function test_get_list_of_kabupaten_kota_based_on_provinsi_id()
    {
        $faker = Faker::create('id_ID');
        $userIds = User::pluck('id_user');
        $user = User::find($faker->randomElement($userIds));

        Sanctum::actingAs($user);
        $provinsiId = 32; //id jawa Barat

        $response = $this->getJson('api/provinsi/' . $provinsiId . '/kabupaten-kota');

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_kabupaten_kota',
                        'nama',
                        'provinsi_id'
                    ]
                ]
            ]
        );
    }
    public function test_get_list_of_kecamatan_based_on_kabupaten_kota_id()
    {
        $faker = Faker::create('id_ID');
        $userIds = User::pluck('id_user');
        $user = User::find($faker->randomElement($userIds));

        Sanctum::actingAs($user);
        $kabupatenKotaiId = 3205; //id Kabupaten Garut

        $response = $this->getJson('api/kabupaten-kota/' . $kabupatenKotaiId . '/kecamatan');

        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_kecamatan',
                        'nama',
                        'kabupaten_kota_id'
                    ]
                ]
            ]
        );
    }

    // public function test_get_all_Kabupaten_Kota_data()
    // {
    //     $response = $this->getJson('api/kabupaten-kota');

    //     $response->assertOk()->assertJsonStructure(
    //         [
    //             'kode',
    //             'status',
    //             'message',
    //             'data' => [
    //                 '*' => [
    //                     'id_kabupaten_kota',
    //                     'nama',
    //                     'provinsi_id',
    //                 ]
    //             ]
    //         ]
    //     );
    // }

    // public function test_get_all_kecamatan_data()
    // {
    //     $response = $this->getJson('api/kecamatan');

    //     $response->assertOk()->assertJsonStructure(
    //         [
    //             'kode',
    //             'status',
    //             'message',
    //             'data' => [
    //                 '*' => [
    //                     'id_kecamatan',
    //                     'nama',
    //                     'kabupaten_kota_id'
    //                 ]
    //             ]
    //         ]
    //     );
    // }
}
