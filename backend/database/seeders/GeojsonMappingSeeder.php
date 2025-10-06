<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeojsonMapping;
use Carbon\Carbon;

class GeojsonMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing mappings for BE10126 first
        GeojsonMapping::where('mas_device_code', 'BE10126')->delete();

        // Create mapping for discharge range 0-10 (welang_debit_10.geojson)
        GeojsonMapping::create([
            'geojson_code' => 'welang_debit_10',
            'mas_device_code' => 'BE10126',
            'value_min' => '0',
            'value_max' => '10',
            'file_path' => realpath(__DIR__ . '/../../../uploads/GIS/geojson_map/welang_debit_10.geojson'),
            'description' => 'Normal water level flood mapping for BE10126 (0-10 m³/s)',
            'version' => '1.0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create mapping for discharge range 11-18 (welang_debit_18.geojson)
        GeojsonMapping::create([
            'geojson_code' => 'welang_debit_18',
            'mas_device_code' => 'BE10126',
            'value_min' => '11',
            'value_max' => '18',
            'file_path' => realpath(__DIR__ . '/../../../uploads/GIS/geojson_map/welang_debit_18.geojson'),
            'description' => 'High water level flood mapping for BE10126 (11-18 m³/s)',
            'version' => '1.0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        echo "✅ GeojsonMapping seeded successfully!\n";
        echo "Created mappings for sensor BE10126:\n";
        echo "- Range 0-10 m³/s → welang_debit_10.geojson\n";
        echo "- Range 11-18 m³/s → welang_debit_18.geojson\n";
    }
}