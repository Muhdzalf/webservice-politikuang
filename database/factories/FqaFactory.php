<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Administrator;
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
            'admin_id' => Administrator::factory()
        ];
    }
}
