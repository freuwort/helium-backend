<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'legal_form' => $this->faker->randomElement(['ag', 'gmbh', 'kg', 'ev', 'sole-proprietor']),
            'description' => $this->faker->catchPhrase(),
            'notes' => $this->faker->paragraph,
        ];
    }
}
