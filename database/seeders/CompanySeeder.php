<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = array(
            array(
                "id" => 1,
                "name" => "Company 1",
                "status" => "active",
            ),
            array(
                "id" => 2,
                "name" => "Company 2",
                "status" => "active",
            ),
            array(
                "id" => 3,
                "name" => "Company 3",
                "status" => "active",
            ),
            array(
                "id" => 4,
                "name" => "Company 4",
                "status" => "active",
            ),
            array(
                "id" => 5,
                "name" => "Company 5",
                "status" => "active",
            ),
            array(
                "id" => 6,
                "name" => "Company 6",
                "status" => "active",
            ),
            array(
                "id" => 7,
                "name" => "Company 7",
                "status" => "active",
            ),
            array(
                "id" => 8,
                "name" => "Company 8",
                "status" => "active",
            ),
            array(
                "id" => 9,
                "name" => "Company 9",
                "status" => "active",
            ),
            array(
                "id" => 10,
                "name" => "Company 10",
                "status" => "active",
            ),
        );
        
        DB::table('companies')->insert($companies);

    }
}
