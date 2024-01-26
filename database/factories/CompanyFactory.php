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
            'legal_form' => $this->faker->randomElement(['AG', 'GmbH', 'KG', 'e.V.']),
            'description' => $this->faker->paragraph,
            'notes' => $this->faker->paragraph,
        ];
    }
}
