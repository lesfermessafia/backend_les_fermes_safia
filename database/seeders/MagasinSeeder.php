<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Magasin;

class MagasinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Magasin::create([
            'nom' => 'Magasin Principal Dakar',
            'idsite' => 1,
            'longitude' => -17.4600,
            'latitude' => 14.7300,
            'longueur' => 50.00,
            'largeur' => 30.00,
            'gerant' => 4,
        ]);

        Magasin::create([
            'nom' => 'Magasin Thiès',
            'idsite' => 2,
            'longitude' => -16.9400,
            'latitude' => 14.8000,
            'longueur' => 40.00,
            'largeur' => 25.00,
            'gerant' => 5,
        ]);
    }
}
