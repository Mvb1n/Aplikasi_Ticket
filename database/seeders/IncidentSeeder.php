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
        $sites = Site::with('assets')->get(); // Ambil site beserta asetnya

        // Pastikan data master ada sebelum melanjutkan
        if ($users->isEmpty() || $sites->isEmpty()) {
            $this->command->info('Tidak ada data user/site yang cukup. Harap jalankan seeder lain terlebih dahulu.');
            return;
        }

        // Buat 15 laporan insiden dummy
        for ($i = 0; $i < 15; $i++) {
            // 1. Pilih satu site acak yang memiliki aset
            $randomSite = $sites->where('assets', '!=', null)->random();
            $assetsInSite = $randomSite->assets->where('status', 'In Use');

            // Lewati jika site ini tidak punya aset yang bisa dilaporkan
            if ($assetsInSite->isEmpty()) {
                continue;
            }

            // Pilih status secara acak
            $status = ['Open', 'In Progress', 'Resolved', 'Closed'][array_rand(['Open', 'In Progress', 'Resolved', 'Closed'])];

            // 2. Buat satu tiket insiden baru untuk site yang dipilih
            $incident = Incident::create([
                'user_id' => $users->random()->id,
                'site_id' => $randomSite->id, // Gunakan ID dari site yang sudah dipilih
                'title' => 'Laporan Kehilangan Aset di ' . $randomSite->name,
                'location' => 'Area Gudang Belakang',
                'chronology' => 'Beberapa aset ditemukan tidak ada di tempatnya saat pengecekan rutin pagi hari.',
                'status' => $status,
            ]);

            // 3. Ambil 1 atau 2 aset secara acak DARI SITE YANG SAMA
            $assetsToReport = $assetsInSite->random(min(rand(1, 2), $assetsInSite->count()));

            // 4. Tautkan aset-aset tersebut ke insiden yang baru dibuat
            $incident->assets()->attach($assetsToReport->pluck('id')->toArray());

            // 5. LOGIKA BARU: Jika statusnya Resolved atau Closed, ubah status aset
            if (in_array($status, ['Resolved', 'Closed'])) {
                foreach ($assetsToReport as $asset) {
                    $asset->status = 'Stolen/Lost';
                    $asset->save();
                }
            }
        }
    }
}