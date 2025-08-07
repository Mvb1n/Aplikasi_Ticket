<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {    
        Site::create(['name' => 'Site Palembang', 'location_code' => 'PLM', 'address' => 'Jl. Jenderal Sudirman No. 1, Palembang','latitude' => -2.9760735,'longitude' => 104.7754253,]);
        Site::create(['name' => 'Site Pangkalan Balai', 'location_code' => 'PBI', 'address' => 'Komplek Perkantoran Pemerintah Kabupaten Banyuasin','latitude' => -2.8681,'longitude' => 104.3887,]);
        Site::create(['name' => 'Site Sekayu', 'location_code' => 'SKY', 'address' => 'Komplek Perkantoran Pemkab Musi Banyuasin','latitude' => -2.8892,'longitude' => 103.8392,]);
        Site::create(['name' => 'Site Indralaya', 'location_code' => 'IDL', 'address' => 'Jl. Gatot Subroto No. 30, Medan','latitude' => -3.2355,'longitude' => 104.6511,]);
        Site::create(['name' => 'Site Prabumulih', 'location_code' => 'PRM', 'address' => 'Jl. Jend. Sudirman No.67','latitude' => -3.4333,'longitude' => 104.2333,]);
    }
}
