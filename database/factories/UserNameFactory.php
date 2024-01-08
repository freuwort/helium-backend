<?php

namespace Database\Factories;

use App\Models\UserName;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserName>
 */
class UserNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'salutation' => fake()->title(),
            'firstname' => fake()->firstName(),
            'middlename' => fake()->optional()->firstName(),
            'lastname' => fake()->lastName(),
            'suffix' => fake()->optional(.1)->suffix(),
        ];
    }



    public function nickname(): static
    {
        return $this->state(fn (array $attributes) => [
            'nickname' => fake()->unique()->firstName(),
        ]);
    }

    public function legalname(): static
    {
        return $this->state(fn (array $attributes) => [
            'legalname' => fake()->unique()->name(),
        ]);
    }
}
