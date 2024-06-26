<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = array(
            array(
                "id" => 1,
                "name" => "Siemens",
                "status" => "active",
            ),
            array(
                "id" => 2,
                "name" => "Weg",
                "status" => "active",
            ),
            array(
                "id" => 3,
                "name" => "Baldor",
                "status" => "active",
            ),
            array(
                "id" => 4,
                "name" => "US Motor",
                "status" => "active",
            ),
            array(
                "id" => 5,
                "name" => "Westinghouse",
                "status" => "active",
            ),
            array(
                "id" => 6,
                "name" => "ABB",
                "status" => "active",
            ),
        );
        DB::table('brands')->insert($brands);
        
    }
}
