<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengawas>
 */
class PengawasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'no_spt' => '01/SK/' . $this->faker->city() . '/V/2023',
            'jabatan' => $this->faker->randomElement(['Ketua Pengawas Desa', 'Ketua Pengawas Kecamatan', 'Anggota Pengawas Desa', 'Anggota Pengawas Kecamatan']),
            'mulai_tugas' => '2023-01-01',
            'selesai_tugas' => '2023-06-01',
        ];
    }
}
