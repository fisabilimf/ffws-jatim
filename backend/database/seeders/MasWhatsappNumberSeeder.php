<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasWhatsappNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numbers = [
            ['name' => 'FFWS Admin', 'number' => '+6281234567890'],
            ['name' => 'Emergency Contact', 'number' => '+6281234567891'],
            ['name' => 'Field Officer 1', 'number' => '+6281234567892'],
            ['name' => 'Field Officer 2', 'number' => '+6281234567893'],
            ['name' => 'District Coordinator', 'number' => '+6281234567894'],
        ];
        
        foreach ($numbers as $number) {
            DB::table('mas_whatsapp_numbers')->insert([
                'name' => $number['name'],
                'number' => $number['number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
