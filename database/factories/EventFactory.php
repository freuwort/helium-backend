<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventInvite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();
        $slug = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->randomHtml(),
            'start_at' => now()->addDays(fake()->numberBetween(1, 7)),
            'end_at' => now()->addDays(fake()->numberBetween(8, 10)),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Event $event) {
            $event->invites()->saveMany(EventInvite::factory(fake()->numberBetween(6, 500))->claim(fake()->boolean(60))->make());
        });
    }

    public function noEnd(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_at' => null,
        ]);
    }
}
