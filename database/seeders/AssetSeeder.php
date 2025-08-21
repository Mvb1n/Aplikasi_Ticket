<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $sites = Site::all();
        if ($sites->isEmpty()) return;

        // Buat 5 Baterai
        for ($i = 1; $i <= 10; $i++) {
            Asset::create([
                'site_id' => $sites->random()->id,
                'category' => 'Baterai',
                'name' => 'Baterai VRLA 12V 100Ah',
                'serial_number' => 'BTR-100-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'In Use'
            ]);
        }

        // Buat 5 Laptop
        for ($i = 1; $i <= 5; $i++) {
            Asset::create([
                'site_id' => $sites->random()->id,
                'category' => 'BTS',
                'name' => 'BTS ' . rand(5000, 9000),
                'serial_number' => 'BTS-' . rand(10000, 99999),
                'status' => 'In Use'
            ]);
        }

        // Buat 10 Testing
        for ($i = 1; $i <= 10; $i++) {
            Asset::create([
                'site_id' => $sites->random()->id,
                'category' => 'Testing',
                'name' => 'Testing ' . rand(5000, 9000),
                'serial_number' => 'Testing-' . rand(10000, 99999),
                'status' => 'In Use'
            ]);
        }
    }
}