<?php

namespace Database\Factories;

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
            'nama' => 'Nama Pemilu Telah diedit',
            'tanggal_pelaksanaan' => $this->faker->date(),
            'jenis_id' => 0, // Jenis Pemilu
            'alamat_id' => 0,
        ];
    }
}
