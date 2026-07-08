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

        User::updateOrCreate(
            ['email' => 'gerant1@fermesafia.com'],
            [
                'nom' => 'Gérant',
                'prenom' => 'Site1',
                'numero' => '774567890',
                'role' => 'comptable',
                'password' => Hash::make('password123'),
                'bloquer' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'gerant2@fermesafia.com'],
            [
                'nom' => 'Gérant',
                'prenom' => 'Ferme1',
                'numero' => '775678901',
                'role' => 'comptable',
                'password' => Hash::make('password123'),
                'bloquer' => false,
            ]
        );
    }
}
