<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::create([
            'nom' => 'Site Principal Dakar',
            'adresse' => 'Dakar, Sénégal',
            'longitude' => -17.4440,
            'latitude' => 14.7167,
            'longueur' => 100.50,
            'largeur' => 50.25,
            'gerant' => 4,
        ]);

        Site::create([
            'nom' => 'Site Thiès',
            'adresse' => 'Thiès, Sénégal',
            'longitude' => -16.9233,
            'latitude' => 14.7833,
            'longueur' => 80.00,
            'largeur' => 40.00,
            'gerant' => 5,
        ]);
    }
}
