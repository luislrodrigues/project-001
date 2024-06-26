<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipmentTypes = array(
            array(
                "id" => 1,
                "name" => "Motor Eléctrico",
                "status" => "active",
            ),
            array(
                "id" => 2,
                "name" => "Motor Combustión",
                "status" => "active",
            ),
            array(
                "id" => 3,
                "name" => "Acople",
                "status" => "active",
            ),
            array(
                "id" => 4,
                "name" => "Transmisión por Correas",
                "status" => "active",
            ),
            array(
                "id" => 5,
                "name" => "Transmisión por Cadena",
                "status" => "active",
            ),
            array(
                "id" => 6,
                "name" => "Reductor",
                "status" => "active",
            ),
            array(
                "id" => 7,
                "name" => "Bomba Centrífuga",
                "status" => "active",
            ),
            array(
                "id" => 8,
                "name" => "Bomba de Paletas",
                "status" => "active",
            ),
            array(
                "id" => 9,
                "name" => "Bomba Engranajes",
                "status" => "active",
            ),
            array(
                "id" => 10,
                "name" => "Bomba PCP",
                "status" => "active",
            ),
            array(
                "id" => 11,
                "name" => "Ventilador",
                "status" => "active",
            ),
            array(
                "id" => 12,
                "name" => "Generador Eléctrico",
                "status" => "active",
            ),
        );

        DB::table('equipment_types')->insert($equipmentTypes);
    }
}
