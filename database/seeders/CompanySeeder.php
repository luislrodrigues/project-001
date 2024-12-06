<?php

namespace Database\Seeders;

use App\Models\Company;
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
                "id"     => 1,
                "name"   => "Company 1",
                "status" => "active",
                "emails" => json_encode(["luisleonardor4@gmail.com"],true),
            ),
            array(
                "id"     => 2,
                "name"   => "Company 2",
                "status" => "active",
                "emails" => json_encode(["luisleonardor4@gmail.com"],true),
            ),
            array(
                "id"     => 3,
                "name"   => "Company 3",
                "status" => "active",
                "emails" => json_encode(["luisleonardor4@gmail.com"],true),
            ),
        );
        
        foreach ($companies as $company) {
            Company::create($company);
        }

    }
}
