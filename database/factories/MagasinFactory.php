<?php

namespace Database\Factories;

use App\Models\Magasin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Magasin>
 */
class MagasinFactory extends Factory
{
    protected $model = Magasin::class;

    public function definition(): array
    {
        return [
            'nom' => 'Magasin ' . fake()->unique()->word(),
            'idsite' => null,
            'latitude' => fake()->latitude(12.5, 16.5),
            'longitude' => fake()->longitude(-17.5, -11.3),
            'longueur' => fake()->randomFloat(2, 20, 300),
            'largeur' => fake()->randomFloat(2, 10, 200),
            'gerant' => null,
        ];
    }
}
