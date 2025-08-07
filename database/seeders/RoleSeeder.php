<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // firstOrCreate akan mencari role dengan nama ini.
        // Jika tidak ada, ia akan membuatnya. Jika sudah ada, ia akan melewatinya.
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'security']);
        Role::firstOrCreate(['name' => 'staff']);
    }
}
