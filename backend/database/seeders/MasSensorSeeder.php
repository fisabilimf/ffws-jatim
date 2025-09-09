<?php

namespace Database\Seeders;

use App\Models\MasSensor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasSensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasSensor::factory(20)->create();
    }
}
