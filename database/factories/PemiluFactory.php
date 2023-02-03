<?php

namespace Database\Factories;

use App\Models\Alamat;
use App\Models\JenisPemilu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pemilu>
 */
class PemiluFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nama' => 'Test Nama Pemilu ' . $this->faker->numberBetween(1, 500),
            'tanggal_pelaksanaan' => $this->faker->dateTimeBetween('now', '+2 months'),
            'jenis_id' => JenisPemilu::factory(),
            'alamat_id' => Alamat::factory()->generateGarutJawaBarat()->create(),
        ];
    }
}
