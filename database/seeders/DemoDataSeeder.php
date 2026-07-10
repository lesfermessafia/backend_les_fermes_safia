<?php

namespace Database\Seeders;

use App\Models\Aliment;
use App\Models\Ferme;
use App\Models\Formule;
use App\Models\Magasin;
use App\Models\MatierePremiere;
use App\Models\Poulet;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedUsers();
        $this->seedMatierePremieres();
        $this->seedEntities();
        $this->seedInventory();
    }

    protected function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'numero' => '771234567',
                'role' => 'admin',
                'password' => Hash::make('admin'),
                'bloquer' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'comptable@example.com'],
            [
                'nom' => 'Comptable',
                'prenom' => 'Test',
                'numero' => '772345678',
                'role' => 'comptable',
                'password' => Hash::make('comptable'),
                'bloquer' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'superviseur@example.com'],
            [
                'nom' => 'Superviseur',
                'prenom' => 'Test',
                'numero' => '773456789',
                'role' => 'superviseur',
                'password' => Hash::make('superviseur'),
                'bloquer' => false,
            ]
        );

        User::factory()->count(20)->create();
    }

    protected function seedMatierePremieres(): void
    {
        MatierePremiere::factory()->count(25)->create();
    }

    protected function seedEntities(): void
    {
        $users = User::all();
        $managers = $users->where('role', 'comptable')->isEmpty() ? $users : $users->where('role', 'comptable');

        Site::factory()->count(8)->state(function () use ($managers) {
            return ['gerant' => $managers->random()->id];
        })->create();

        $sites = Site::all();

        Ferme::factory()->count(15)->state(function () use ($sites, $managers) {
            return [
                'idsite' => $sites->random()->id,
                'gerant' => $managers->random()->id,
            ];
        })->create();

        Magasin::factory()->count(12)->state(function () use ($sites, $managers) {
            return [
                'idsite' => $sites->random()->id,
                'gerant' => $managers->random()->id,
            ];
        })->create();
    }

    protected function seedInventory(): void
    {
        Aliment::factory()->count(20)->create();
        Poulet::factory()->count(15)->create();

        $matieres = MatierePremiere::all();

        Formule::factory()->count(10)->state(function () use ($matieres) {
            $composants = $matieres->random(rand(2, 5))->map(function ($matiere) {
                return [
                    'matiere_id' => $matiere->id,
                    'quantite' => rand(5, 95),
                ];
            })->values()->toArray();

            return ['composant' => $composants];
        })->create();
    }
}
