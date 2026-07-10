<?php

namespace Database\Factories;

use App\Models\Poulet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poulet>
 */
class PouletFactory extends Factory
{
    protected $model = Poulet::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->word(),
            'code' => Poulet::generateCode(),
            'race' => fake()->randomElement(['Poulet de chair', 'Poulet pondeuse', 'Poulet Brahma', 'Poulet Label Rouge', 'Poulet de race']),
            'photo' => null,
        ];
    }
}
