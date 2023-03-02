<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Laravel\Sanctum\Sanctum;

class AdministratorTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
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

        $response = $this->postJson('api/admin/create', $AdminData);

        $response->assertOk()->dump();
    }
}
