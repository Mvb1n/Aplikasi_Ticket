<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Incident;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data master yang kita butuhkan
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'staff');
        })->get();
        $sites = Site::all();
        $assets = Asset::where('status', 'In Use')->get();

        // Pastikan data master ada sebelum melanjutkan
        if ($users->isEmpty() || $sites->isEmpty() || $assets->count() < 3) {
            $this->command->info('Tidak cukup data user/site/asset untuk membuat insiden. Harap jalankan seeder lain terlebih dahulu.');
            return;
        }

        // Buat 15 laporan insiden dummy
        for ($i = 0; $i < 15; $i++) {
            // 1. Buat satu tiket insiden baru
            $incident = Incident::create([
                'user_id' => $users->random()->id, // Pilih user staff secara acak
                'site_id' => $sites->random()->id, // Pilih site secara acak
                'title' => 'Laporan Kehilangan Aset #' . ($i + 1),
                'location' => 'Area Gudang Belakang',
                'chronology' => 'Beberapa aset ditemukan tidak ada di tempatnya saat pengecekan rutin pagi hari.',
                'status' => ['Open', 'In Progress', 'Resolved', 'Closed', 'Cancelled'][array_rand(['Open', 'In Progress', 'Resolved', 'Closed', 'Cancelled'])],
            ]);

            // 2. Ambil 1 sampai 3 aset secara acak untuk ditautkan ke insiden ini
            $randomAssets = $assets->random(rand(1, 3));

            // 3. Tautkan aset-aset tersebut ke insiden yang baru dibuat
            $incident->assets()->attach($randomAssets->pluck('id')->toArray());
        }
    }
}