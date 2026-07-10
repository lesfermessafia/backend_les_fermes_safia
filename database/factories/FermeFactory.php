<?php

namespace Database\Factories;

use App\Models\Ferme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ferme>
 */
class FermeFactory extends Factory
{
    protected $model = Ferme::class;

    public function definition(): array
    {
        return [
            'nom' => 'Ferme ' . fake()->unique()->word(),
            'idsite' => null,
            'latitude' => fake()->latitude(12.5, 16.5),
            'longitude' => fake()->longitude(-17.5, -11.3),
            'longueur' => fake()->randomFloat(2, 20, 500),
            'largeur' => fake()->randomFloat(2, 10, 300),
            'gerant' => null,
        ];
    }
}
