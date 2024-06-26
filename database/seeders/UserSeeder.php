<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                "password" => bcrypt(12345678)
            ),
            array(
                "name" => "alvaro",
                "email" => "alvaro@email.com",
                "status" => "active",
                "password" => bcrypt(12345678)
            ),
        );
        

        DB::table('users')->insert($users);
    }
}
