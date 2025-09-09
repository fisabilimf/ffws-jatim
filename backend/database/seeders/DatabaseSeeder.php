<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AdminSeeder::class,
            MasRiverBasinSeeder::class,
            ModelDataSeeder::class,  // Models need to exist before devices can reference them
            MasDeviceSeeder::class,
            MasSensorSeeder::class,
            DataActualSeeder::class,
            DataPredictionSeeder::class,  // Added prediction seeder
            ScalerDataSeeder::class,
            MasWhatsappNumberSeeder::class,  // Added whatsapp number seeder
        ]);
    }
}
