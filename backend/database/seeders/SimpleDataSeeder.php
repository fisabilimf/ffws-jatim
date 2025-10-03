<?php

namespace Database\Seeders;

use App\Models\MasProvince;
use App\Models\MasCity;
use App\Models\MasRegency;
use App\Models\MasVillage;
use Illuminate\Database\Seeder;

class SimpleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create provinces
        $provinces = [
            ['provinces_name' => 'Jawa Timur', 'provinces_code' => 'JT001'],
            ['provinces_name' => 'Jawa Tengah', 'provinces_code' => 'JTG001'],
        ];
        
        foreach ($provinces as $provinceData) {
            MasProvince::firstOrCreate(['provinces_code' => $provinceData['provinces_code']], $provinceData);
        }

        // Create cities
        $cities = [
            ['cities_name' => 'Malang', 'cities_code' => 'MLG001'],
            ['cities_name' => 'Batu', 'cities_code' => 'BTU001'],
            ['cities_name' => 'Blitar', 'cities_code' => 'BLT001'],
            ['cities_name' => 'Surabaya', 'cities_code' => 'SBY001'],
        ];
        
        foreach ($cities as $cityData) {
            MasCity::firstOrCreate(['cities_code' => $cityData['cities_code']], $cityData);
        }

        // Create regencies
        $regencies = [
            ['regencies_name' => 'Malang Kota', 'regencies_code' => 'MLK001'],
            ['regencies_name' => 'Malang Kabupaten', 'regencies_code' => 'MLK002'],
            ['regencies_name' => 'Batu Kota', 'regencies_code' => 'BTK001'],
        ];
        
        foreach ($regencies as $regencyData) {
            MasRegency::firstOrCreate(['regencies_code' => $regencyData['regencies_code']], $regencyData);
        }

        // Create villages
        $villages = [
            ['villages_name' => 'Lowokwaru', 'villages_code' => 'LWK001'],
            ['villages_name' => 'Klojen', 'villages_code' => 'KLJ001'],
            ['villages_name' => 'Blimbing', 'villages_code' => 'BLM001'],
        ];
        
        foreach ($villages as $villageData) {
            MasVillage::firstOrCreate(['villages_code' => $villageData['villages_code']], $villageData);
        }

        echo "Simple master data seeded successfully!\n";
    }
}