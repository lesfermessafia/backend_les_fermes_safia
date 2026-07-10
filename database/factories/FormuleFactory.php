<?php

namespace Database\Factories;

use App\Models\Formule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Formule>
 */
class FormuleFactory extends Factory
{
    protected $model = Formule::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->words(2, true),
            'photo' => null,
            'composant' => [],
        ];
    }
}
