<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunitiesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('rol', 'admin')->first() ?? User::first();

        $carreras = [
            ['name' => 'Ingeniería en Sistemas Computacionales', 'icon' => '💻'],
            ['name' => 'Ingeniería Electromecánica',             'icon' => '⚡'],
            ['name' => 'Ingeniería Civil',                       'icon' => '🏗️'],
            ['name' => 'Ingeniería en Gestión Empresarial',      'icon' => '📊'],
            ['name' => 'Licenciatura en Arquitectura',           'icon' => '🏛️'],
            ['name' => 'Ingeniería Mecatrónica',                 'icon' => '🤖'],
            ['name' => 'Ingeniería en Industrias Alimentarias',  'icon' => '🍎'],
            ['name' => 'Ingeniería Sistemas Automotrices',       'icon' => '🚗'],
            ['name' => 'Ingeniería Industrial',                  'icon' => '🏭'],
        ];

        foreach ($carreras as $carrera) {
            Community::firstOrCreate(
                ['slug' => Str::slug($carrera['name'])],
                [
                    'name'        => $carrera['name'],
                    'icon'        => $carrera['icon'],
                    'description' => "Comunidad oficial de {$carrera['name']} del TSJ Lagos.",
                    'tipo'        => 'carrera',
                    'created_by'  => $admin->id,
                ]
            );
        }

        // Comunidad de Avisos (solo admins pueden publicar)
        Community::firstOrCreate(
            ['slug' => 'avisos-institucionales'],
            [
                'name'        => 'Avisos Institucionales',
                'icon'        => '📢',
                'description' => 'Avisos y comunicados oficiales del TSJ Lagos. Solo administradores pueden publicar.',
                'tipo'        => 'avisos',
                'created_by'  => $admin->id,
            ]
        );
    }
}
