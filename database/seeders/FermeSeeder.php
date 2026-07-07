<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ferme;

class FermeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ferme::create([
            'nom' => 'Ferme Nord Dakar',
            'idsite' => 1,
            'longitude' => -17.4500,
            'latitude' => 14.7200,
            'longueur' => 200.00,
            'largeur' => 100.00,
            'gerant' => 4,
        ]);

        Ferme::create([
            'nom' => 'Ferme Sud Thiès',
            'idsite' => 2,
            'longitude' => -16.9300,
            'latitude' => 14.7900,
            'longueur' => 150.00,
            'largeur' => 75.00,
            'gerant' => 5,
        ]);
    }
}
