<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Único punto de entrada para sembrar la aplicación.
 * Ejecuta en orden: roles/permisos, usuarios, tipos de componente, marcas, campos de tipo.
 */
class AppSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ComponentTypeSeeder::class,
            BrandSeeder::class,
            ComponentTypeFieldSeeder::class,
        ]);
    }
}
