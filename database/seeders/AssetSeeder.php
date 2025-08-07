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

        // Buat 10 Baterai
        for ($i = 1; $i <= 10; $i++) {
            Asset::create([
                'site_id' => $sites->random()->id,
                'category' => 'Baterai',
                'name' => 'Baterai VRLA 12V 100Ah',
                'serial_number' => 'BTR-100-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'In Use'
            ]);
        }

        // Buat 15 Aset Lainnya
        for ($i = 1; $i <= 15; $i++) {
            Asset::create([
                'site_id' => $sites->random()->id,
                'category' => 'Perangkat IT',
                'name' => 'Laptop Dell Latitude ' . rand(5000, 9000),
                'serial_number' => 'DELL-' . rand(10000, 99999),
                'status' => 'In Use'
            ]);
        }
    }
}