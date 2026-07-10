<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'numero' => '7' . fake()->numberBetween(60_000_000, 89_999_999),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['admin', 'comptable', 'superviseur']),
            'password' => static::$password ??= Hash::make('password'),
            'photo_profil' => null,
            'bloquer' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'bloquer' => false,
        ]);
    }
}
