<?php

namespace Database\Seeders;

use App\Models\DataActual;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataActualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DataActual::factory(100)->create();
    }
}
