<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserCompany;
use App\Models\UserName;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'ident_number' => fake()->unique()->numerify('K-##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
            'enabled_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->user_name()->save(UserName::factory()->make());
            $user->user_company()->save(UserCompany::factory()->make());
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model is not enabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled_at' => null,
        ]);
    }

    /**
     * Indicate that the model is soft deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
