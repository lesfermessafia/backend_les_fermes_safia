<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lot;

class LotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lot1 = Lot::create([
            'code_lot' => '050726-01',
        ]);

        $lot1->matierePremieres()->attach(1, ['quantite' => 100.00]);
        $lot1->matierePremieres()->attach(2, ['quantite' => 50.00]);

        $lot2 = Lot::create([
            'code_lot' => '050726-02',
        ]);

        $lot2->matierePremieres()->attach(3, ['quantite' => 30.00]);
        $lot2->matierePremieres()->attach(4, ['quantite' => 20.00]);

        $lot3 = Lot::create([
            'code_lot' => '050726-03',
        ]);

        $lot3->matierePremieres()->attach(1, ['quantite' => 150.00]);
        $lot3->matierePremieres()->attach(5, ['quantite' => 25.00]);
    }
}
