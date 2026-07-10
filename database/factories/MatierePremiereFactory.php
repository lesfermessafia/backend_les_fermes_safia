<?php

namespace Database\Factories;

use App\Models\MatierePremiere;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatierePremiere>
 */
class MatierePremiereFactory extends Factory
{
    protected $model = MatierePremiere::class;

    public function definition(): array
    {
        $nom = fake()->unique()->word();
        $matiere = new MatierePremiere(['nom' => $nom]);

        return [
            'nom' => $nom,
            'code' => $matiere->generateUniqueCode(),
            'image' => null,
            'unite' => fake()->randomElement(['kg', 'L', 'g', 'unité', 'sac', 'boîte']),
        ];
    }
}
