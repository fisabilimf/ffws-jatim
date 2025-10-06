<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\GeojsonMapping;

echo "Verifying GeojsonMapping records for BE10126:\n";
echo "=============================================\n";

$mappings = GeojsonMapping::where('mas_device_code', 'BE10126')->get();
echo "Found " . $mappings->count() . " mappings\n\n";

foreach($mappings as $mapping) {
    echo "ID: " . $mapping->id . "\n";
    echo "Code: " . $mapping->geojson_code . "\n";
    echo "Device: " . $mapping->mas_device_code . "\n";
    echo "Range: " . $mapping->value_min . " - " . $mapping->value_max . " m³/s\n";
    echo "File: " . $mapping->file_path . "\n";
    echo "Description: " . $mapping->description . "\n";
    echo "---\n";
}

echo "\nSystem ready! You can now test the API endpoints.\n";