<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MouvementStock;

class MouvementStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MouvementStock::create([
            'magasin_id' => 1,
            'matiere_id' => 1,
            'lot_id' => 1,
            'type' => 'entree',
            'quantite' => 100.00,
            'date_mouvement' => '2026-07-01',
            'gerant_id' => 4,
            'observation' => 'Entrée initiale',
        ]);

        MouvementStock::create([
            'magasin_id' => 1,
            'matiere_id' => 1,
            'lot_id' => 1,
            'type' => 'sortie',
            'quantite' => 30.00,
            'date_mouvement' => '2026-07-03',
            'gerant_id' => 4,
            'observation' => 'Sortie pour production',
        ]);

        MouvementStock::create([
            'magasin_id' => 1,
            'matiere_id' => 2,
            'lot_id' => 1,
            'type' => 'entree',
            'quantite' => 50.00,
            'date_mouvement' => '2026-07-02',
            'gerant_id' => 4,
            'observation' => 'Entrée de sucre',
        ]);

        MouvementStock::create([
            'magasin_id' => 2,
            'matiere_id' => 3,
            'lot_id' => 2,
            'type' => 'entree',
            'quantite' => 30.00,
            'date_mouvement' => '2026-07-04',
            'gerant_id' => 5,
            'observation' => 'Entrée d\'huile',
        ]);

        MouvementStock::create([
            'magasin_id' => 2,
            'matiere_id' => 4,
            'lot_id' => 2,
            'type' => 'entree',
            'quantite' => 20.00,
            'date_mouvement' => '2026-07-04',
            'gerant_id' => 5,
            'observation' => 'Entrée de levure',
        ]);

        MouvementStock::create([
            'magasin_id' => 1,
            'matiere_id' => 1,
            'lot_id' => 3,
            'type' => 'entree',
            'quantite' => 150.00,
            'date_mouvement' => '2026-07-05',
            'gerant_id' => 4,
            'observation' => 'Nouvelle entrée de farine',
        ]);
    }
}
