<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Principal',
            'numero' => '771234567',
            'role' => 'admin',
            'email' => 'admin@fermesafia.com',
            'password' => Hash::make('password123'),
            'bloquer' => false,
        ]);

        User::create([
            'nom' => 'Comptable',
            'prenom' => 'Test',
            'numero' => '772345678',
            'role' => 'comptable',
            'email' => 'comptable@fermesafia.com',
            'password' => Hash::make('password123'),
            'bloquer' => false,
        ]);

        User::create([
            'nom' => 'Superviseur',
            'prenom' => 'Test',
            'numero' => '773456789',
            'role' => 'superviseur',
            'email' => 'superviseur@fermesafia.com',
            'password' => Hash::make('password123'),
            'bloquer' => false,
        ]);

        User::create([
            'nom' => 'Gérant',
            'prenom' => 'Site1',
            'numero' => '774567890',
            'role' => 'comptable',
            'email' => 'gerant1@fermesafia.com',
            'password' => Hash::make('password123'),
            'bloquer' => false,
        ]);

        User::create([
            'nom' => 'Gérant',
            'prenom' => 'Ferme1',
            'numero' => '775678901',
            'role' => 'comptable',
            'email' => 'gerant2@fermesafia.com',
            'password' => Hash::make('password123'),
            'bloquer' => false,
        ]);
    }
}
