<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MatierePremiere;

class MatierePremiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MatierePremiere::create([
            'nom' => 'Farine',
            'code' => 'FAR-AB',
            'image' => 'farine.jpg',
            'unite' => 'kg',
        ]);

        MatierePremiere::create([
            'nom' => 'Sucre',
            'code' => 'SUC-CD',
            'image' => 'sucre.jpg',
            'unite' => 'kg',
        ]);

        MatierePremiere::create([
            'nom' => 'Huile',
            'code' => 'HUI-EF',
            'image' => 'huile.jpg',
            'unite' => 'L',
        ]);

        MatierePremiere::create([
            'nom' => 'Levure',
            'code' => 'LEV-GH',
            'image' => 'levure.jpg',
            'unite' => 'kg',
        ]);

        MatierePremiere::create([
            'nom' => 'Sel',
            'code' => 'SEL-IJ',
            'image' => 'sel.jpg',
            'unite' => 'kg',
        ]);
    }
}
