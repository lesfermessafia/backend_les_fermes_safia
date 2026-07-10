<?php

namespace Database\Factories;

use App\Models\Aliment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aliment>
 */
class AlimentFactory extends Factory
{
    protected $model = Aliment::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->word(),
            'code' => Aliment::generateCode(),
            'photo' => null,
        ];
    }
}
