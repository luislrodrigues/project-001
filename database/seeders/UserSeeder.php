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
        $users = array(
            array(
                "name" => "luis",
                "email" => "lrodrguez@email.com",
                "status" => "active",
                "password" => bcrypt(12345678),
            ),
            array(
                "name" => "alvaro",
                "email" => "alvaro.sanchezm1@gmail.com",
                "status" => "active",
                "password" => bcrypt(12345678),
            ),
        );
        foreach ($users as $user) {
            User::create($user);
        }
        // Obtener los roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Asignar roles a los usuarios
        User::where('email', 'alvaro.sanchezm1@gmail.com')->first()->assignRole($adminRole);
        User::where('email', 'lrodrguez@email.com')->first()->assignRole($userRole);
    }
}
