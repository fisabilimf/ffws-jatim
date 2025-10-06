<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GeojsonMapping;

echo "=== Testing GeoJSON Mapping System ===\n\n";

// Test 1: Check if mappings exist for BE10126
echo "1. Checking mappings for sensor BE10126:\n";
$mappings = GeojsonMapping::where('mas_device_code', 'BE10126')->get();
echo "Found " . $mappings->count() . " mappings:\n";
foreach ($mappings as $mapping) {
    echo "  - Range: {$mapping->value_min}-{$mapping->value_max} m³/s → {$mapping->file_path}\n";
}
echo "\n";

// Test 2: Test findByDeviceAndDischarge method with discharge value 5
echo "2. Testing discharge value 5 m³/s (should return welang_debit_10.geojson):\n";
$mapping = GeojsonMapping::findByDeviceAndDischarge('BE10126', 5);
if ($mapping) {
    echo "  ✅ Found mapping: {$mapping->file_path}\n";
    echo "  📊 Range: {$mapping->value_min}-{$mapping->value_max} m³/s\n";
} else {
    echo "  ❌ No mapping found\n";
}
echo "\n";

// Test 3: Test findByDeviceAndDischarge method with discharge value 15
echo "3. Testing discharge value 15 m³/s (should return welang_debit_18.geojson):\n";
$mapping = GeojsonMapping::findByDeviceAndDischarge('BE10126', 15);
if ($mapping) {
    echo "  ✅ Found mapping: {$mapping->file_path}\n";
    echo "  📊 Range: {$mapping->value_min}-{$mapping->value_max} m³/s\n";
} else {
    echo "  ❌ No mapping found\n";
}
echo "\n";

// Test 4: Test edge case - discharge value 10 (boundary)
echo "4. Testing boundary value 10 m³/s:\n";
$mapping = GeojsonMapping::findByDeviceAndDischarge('BE10126', 10);
if ($mapping) {
    echo "  ✅ Found mapping: {$mapping->file_path}\n";
    echo "  📊 Range: {$mapping->value_min}-{$mapping->value_max} m³/s\n";
} else {
    echo "  ❌ No mapping found\n";
}
echo "\n";

// Test 5: Test edge case - discharge value 11 (boundary)
echo "5. Testing boundary value 11 m³/s:\n";
$mapping = GeojsonMapping::findByDeviceAndDischarge('BE10126', 11);
if ($mapping) {
    echo "  ✅ Found mapping: {$mapping->file_path}\n";
    echo "  📊 Range: {$mapping->value_min}-{$mapping->value_max} m³/s\n";
} else {
    echo "  ❌ No mapping found\n";
}
echo "\n";

echo "=== Test Complete ===\n";