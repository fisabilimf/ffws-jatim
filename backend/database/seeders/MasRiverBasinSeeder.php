<?php

namespace Database\Seeders;

use App\Models\MasRiverBasin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasRiverBasinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasRiverBasin::factory(5)->create();
    }
}
