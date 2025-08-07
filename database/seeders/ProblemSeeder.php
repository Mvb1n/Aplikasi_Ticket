<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Problem;
use App\Models\Incident;

class ProblemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua insiden yang ada
        $incidents = Incident::all();

        // Pastikan ada insiden untuk ditautkan
        if ($incidents->count() < 3) {
            $this->command->info('Tidak cukup insiden untuk membuat problem. Harap jalankan IncidentSeeder terlebih dahulu.');
            return;
        }

        // Buat 5 tiket problem dummy
        for ($i = 1; $i <= 5; $i++) {
            // Buat satu problem
            $problem = Problem::create([
                'title' => 'Analisis Keamanan Area #' . $i,
                'description' => 'Terjadi beberapa insiden di area ini yang memerlukan analisis akar masalah lebih lanjut.',
                'status' => ['Analysis', 'Solution Implemented', 'Closed'][array_rand(['Analysis', 'Solution Implemented', 'Closed'])],
            ]);

            // Ambil 2 atau 3 insiden secara acak untuk ditautkan ke problem ini
            $randomIncidents = $incidents->random(rand(2, 3));

            // Tautkan insiden-insiden tersebut ke problem yang baru dibuat
            $problem->incidents()->attach($randomIncidents->pluck('id')->toArray());
        }
    }
}
