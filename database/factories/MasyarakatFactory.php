<?php

namespace Database\Factories;

use App\Models\Alamat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Masyarakat>
 */
class MasyarakatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nik' => $this->faker->nik(),
            'tanggal_lahir' => '2000-12-12',
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat_id' => Alamat::factory(),
            'pekerjaan' => $this->faker->jobTitle(),
            'kewarganegaraan' => 'Indonesia',
            'user_id' => User::factory(),
        ];
    }
}
