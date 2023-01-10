<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alamat>
 */
class AlamatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_kota_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32,
        ];
    }

    public function generateGarutJawaBarat()
    {
        return $this->state(function (array $attributes) {
            return [
                'kecamatan_id' => 3205230, // Banyuresmi
                'kabupaten_kota_id' => 3205, // Kabupaten Garut
                'provinsi_id' => 32,
                'desa' => 'Desa ' . $this->faker->numberBetween(0, 100)
            ];
        });
    }
}
