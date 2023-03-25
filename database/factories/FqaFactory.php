<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fqa>
 */
class FqaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pertanyaan' => $this->faker->sentence(),
            'Jawaban' => $this->faker->paragraph(),
            'id_admin' => Admin::factory()
        ];
    }
}
