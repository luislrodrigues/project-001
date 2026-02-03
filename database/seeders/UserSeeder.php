<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = '12345678';
        $users = array(
            array(
                "name" => "luis",
                "email" => "lrodrguez@email.com",
                "status" => "active",
                "password" => bcrypt($password),
                "raw_password" => $password,
                "company_id" => 1,
                "workstation" => "Usuario",
                "area" => "General",
            ),
            array(
                "name" => "alvaro",
                "email" => "alvaro.sanchezm1@gmail.com",
                "status" => "active",
                "password" => bcrypt($password),
                "raw_password" => $password,
                "company_id" => 1,
                "workstation" => "Administrador",
                "area" => "General",
            ),
        );
        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
        // Asignar roles: Administrador (alvaro) y Supervisor (luis)
        $adminRole = Role::where('name', 'Administrador')->first();

        if ($adminRole) {
            User::where('email', 'alvaro.sanchezm1@gmail.com')->first()?->assignRole($adminRole);
        }
    }
}
