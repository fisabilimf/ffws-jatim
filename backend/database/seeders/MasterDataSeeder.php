<?php

namespace Database\Seeders;

use App\Models\MasCity;
use App\Models\MasRiverBasin;
use App\Models\MasWatershed;
use App\Models\MasUpt;
use App\Models\MasDevice;
use App\Models\MasSensor;
use App\Models\MasDeviceParameter;
use App\Models\MasSensorParameter;
use App\Models\DeviceValue;
use App\Models\SensorValue;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cities
        $cities = [
            ['cities_name' => 'Malang', 'cities_code' => 'MLG001'],
            ['cities_name' => 'Batu', 'cities_code' => 'BTU001'],
            ['cities_name' => 'Blitar', 'cities_code' => 'BLT001'],
        ];
        
        foreach ($cities as $cityData) {
            MasCity::firstOrCreate(['cities_code' => $cityData['cities_code']], $cityData);
        }

        // Create river basins
        $riverBasins = [
            ['river_basins_name' => 'Brantas River Basin', 'river_basins_code' => 'BRS001', 'cities_code' => 'MLG001'],
            ['river_basins_name' => 'Bengawan Solo River Basin', 'river_basins_code' => 'BSS001', 'cities_code' => 'BLT001'],
        ];

        foreach ($riverBasins as $basinData) {
            MasRiverBasin::firstOrCreate(['river_basins_code' => $basinData['river_basins_code']], $basinData);
        }

        // Create watersheds
        $watersheds = [
            ['river_basin_code' => 'BRS001', 'watersheds_name' => 'Upper Brantas', 'watersheds_code' => 'UBR001'],
            ['river_basin_code' => 'BRS001', 'watersheds_name' => 'Lower Brantas', 'watersheds_code' => 'LBR001'],
        ];

        foreach ($watersheds as $watershedData) {
            MasWatershed::firstOrCreate(['watersheds_code' => $watershedData['watersheds_code']], $watershedData);
        }

        // Create UPTs
        $upts = [
            ['river_basin_code' => 'BRS001', 'cities_code' => 'MLG001', 'upts_name' => 'UPT SDA Malang', 'upts_code' => 'UPT001'],
            ['river_basin_code' => 'BSS001', 'cities_code' => 'BLT001', 'upts_name' => 'UPT SDA Blitar', 'upts_code' => 'UPT002'],
        ];

        foreach ($upts as $uptData) {
            MasUpt::firstOrCreate(['upts_code' => $uptData['upts_code']], $uptData);
        }

        // Create device parameters
        $deviceParams = [
            ['name' => 'Water Level Monitoring', 'code' => 'WLM001'],
            ['name' => 'Rainfall Monitoring', 'code' => 'RFM001'],
            ['name' => 'Flow Rate Monitoring', 'code' => 'FRM001'],
        ];

        foreach ($deviceParams as $paramData) {
            MasDeviceParameter::firstOrCreate(['code' => $paramData['code']], $paramData);
        }

        // Create sensor parameters
        $sensorParams = [
            ['name' => 'Water Level', 'code' => 'WL001'],
            ['name' => 'Rainfall', 'code' => 'RF001'],
            ['name' => 'Flow Rate', 'code' => 'FR001'],
        ];

        foreach ($sensorParams as $paramData) {
            MasSensorParameter::firstOrCreate(['code' => $paramData['code']], $paramData);
        }

        // Create devices
        $devices = [
            ['code' => 'DEV001', 'name' => 'Device 1', 'latitude' => -7.966620, 'longitude' => 112.632632, 'elevation_m' => 100, 'status' => 'active'],
            ['code' => 'DEV002', 'name' => 'Device 2', 'latitude' => -7.977620, 'longitude' => 112.642632, 'elevation_m' => 110, 'status' => 'active'],
            ['code' => 'DEV003', 'name' => 'Device 3', 'latitude' => -7.988620, 'longitude' => 112.652632, 'elevation_m' => 120, 'status' => 'active'],
        ];

        foreach ($devices as $deviceData) {
            // Need to add foreign key reference
            $deviceData['mas_river_basin_id'] = 1; // Assuming river basin with ID 1 exists
            MasDevice::firstOrCreate(['code' => $deviceData['code']], $deviceData);
        }

        // Create sensors
        $sensors = [
            ['mas_device_code' => 'DEV001', 'sensor_code' => 'SNS001'],
            ['mas_device_code' => 'DEV001', 'sensor_code' => 'SNS002'],
            ['mas_device_code' => 'DEV002', 'sensor_code' => 'SNS003'],
        ];

        foreach ($sensors as $sensorData) {
            MasSensor::firstOrCreate(['sensor_code' => $sensorData['sensor_code']], $sensorData);
        }

        // Create device values (for location and metadata)
        $deviceValues = [
            [
                'mas_device_code' => 'DEV001',
                'mas_river_basin_code' => 'BRS001',
                'mas_watershed_code' => 'UBR001',
                'mas_city_code' => 'MLG001',
                'mas_upt_code' => 'UPT001',
                'mas_device_parameter_code' => 'WLM001',
                'name' => 'Malang Water Level Station',
                'latitude' => -7.9666,
                'longitude' => 112.6326,
                'elevation' => 440.5,
                'status' => 'active',
            ],
            [
                'mas_device_code' => 'DEV002',
                'mas_river_basin_code' => 'BRS001',
                'mas_watershed_code' => 'LBR001',
                'mas_city_code' => 'MLG001',
                'mas_upt_code' => 'UPT001',
                'mas_device_parameter_code' => 'RFM001',
                'name' => 'Malang Rainfall Station',
                'latitude' => -7.9756,
                'longitude' => 112.6426,
                'elevation' => 450.0,
                'status' => 'active',
            ],
        ];

        foreach ($deviceValues as $deviceValueData) {
            DeviceValue::firstOrCreate(
                ['mas_device_code' => $deviceValueData['mas_device_code']], 
                $deviceValueData
            );
        }

        // Create sensor values (for sensor metadata)
        $sensorValues = [
            [
                'mas_sensor_code' => 'SNS001',
                'mas_sensor_parameter_code' => 'WL001',
                'sensor_name' => 'Water Level Sensor 1',
                'sensor_unit' => 'm',
                'sensor_description' => 'Primary water level sensor',
                'status' => 'active',
            ],
            [
                'mas_sensor_code' => 'SNS002',
                'mas_sensor_parameter_code' => 'RF001',
                'sensor_name' => 'Rainfall Sensor 1',
                'sensor_unit' => 'mm',
                'sensor_description' => 'Primary rainfall sensor',
                'status' => 'active',
            ],
        ];

        foreach ($sensorValues as $sensorValueData) {
            SensorValue::firstOrCreate(
                ['mas_sensor_code' => $sensorValueData['mas_sensor_code']], 
                $sensorValueData
            );
        }
    }
}