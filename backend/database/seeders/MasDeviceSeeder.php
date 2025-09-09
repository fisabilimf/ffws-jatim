<?php

namespace Database\Seeders;

use App\Models\MasDevice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasDevice::factory(10)->create();
    }
}
