<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventInvite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventInviteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['VIP', 'Visitor +', 'Guest', null]),
            'name' => fake()->unique()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->e164PhoneNumber(),
            'code' => Str::random(32),
            'status' => fake()->randomElement(['maybe', 'accepted', 'rejected', null]),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function maybe(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maybe',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    public function claim(Bool $claim = true): static
    {
        return $this->state(fn (array $attributes) => [
            'needs_claim' => $claim,
        ]);
    }
}
