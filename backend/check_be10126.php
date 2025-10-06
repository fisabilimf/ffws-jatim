<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking BE10126 ===\n";

// Check if BE10126 is a device code
$device = \App\Models\MasDevice::where('code', 'BE10126')->first();
if ($device) {
    echo "✅ Found as DEVICE: {$device->name}\n";
}

// Check if BE10126 is a sensor code  
$sensor = \App\Models\MasSensor::where('sensor_code', 'BE10126')->first();
if ($sensor) {
    echo "✅ Found as SENSOR, belongs to device: {$sensor->mas_device_code}\n";
}

if (!$device && !$sensor) {
    echo "❌ BE10126 not found as device or sensor\n";
    echo "Let's see what devices and sensors exist:\n";
    
    $devices = \App\Models\MasDevice::limit(5)->get();
    echo "\nSample devices:\n";
    foreach ($devices as $dev) {
        echo "  - {$dev->code}: {$dev->name}\n";
    }
    
    $sensors = \App\Models\MasSensor::limit(5)->get();
    echo "\nSample sensors:\n";
    foreach ($sensors as $sens) {
        echo "  - {$sens->sensor_code} (device: {$sens->mas_device_code})\n";
    }
}