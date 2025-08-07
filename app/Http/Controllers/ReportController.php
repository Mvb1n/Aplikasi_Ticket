<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Incident;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
        public function index(Request $request)
    {
        // Tentukan tanggal default: 30 hari terakhir
        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Ambil data insiden dalam rentang tanggal yang dipilih
        $incidents = Incident::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // 1. Data untuk Grafik Tren Harian
        $incidentTrend = (clone $incidents) // Clone agar tidak mempengaruhi query lain
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Data untuk Ringkasan per Site
        $incidentsBySite = (clone $incidents)
            ->with('site')
            ->selectRaw('site_id, COUNT(*) as count')
            ->groupBy('site_id')
            ->orderBy('count', 'desc')
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'incidentTrend',
            'incidentsBySite'
        ));
    }
}
