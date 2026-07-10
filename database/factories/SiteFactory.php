<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->city() . ' ' . fake()->randomElement(['Site', 'Centre']),
            'adresse' => fake()->address(),
            'latitude' => fake()->latitude(12.5, 16.5),
            'longitude' => fake()->longitude(-17.5, -11.3),
            'longueur' => fake()->randomFloat(2, 20, 500),
            'largeur' => fake()->randomFloat(2, 10, 300),
            'gerant' => null,
        ];
    }
}
