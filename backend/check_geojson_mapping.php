<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\GeojsonMapping;

echo "Current GeojsonMapping records:\n";
echo "==============================\n";

$mappings = GeojsonMapping::all();
echo "Total records: " . $mappings->count() . "\n\n";

foreach($mappings as $m) {
    echo "ID: " . $m->id . "\n";
    echo "Code: " . $m->geojson_code . "\n";
    echo "Device: " . $m->mas_device_code . "\n";
    echo "Range: " . $m->value_min . " - " . $m->value_max . "\n"; 
    echo "File Path: " . $m->file_path . "\n";
    echo "Description: " . $m->description . "\n";
    echo "---\n";
}