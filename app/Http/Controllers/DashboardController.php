<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Problem;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $stats = [];
        $activities = collect();
        $sitesForMap = collect();
        $monthlyIncidentTrend = collect();
        $topProblematicSites = collect();        

        if ($user->hasRole('admin') || $user->hasRole('security')) {
            // Data Statistik Utama
            $stats = [
                'open_incidents' => Incident::where('status', 'Open')->count(),
                'analysis_problems' => Problem::where('status', 'Analysis')->count(),
                'total_assets' => Asset::count(),
                'total_sites' => Site::count(),
            ];

            // Data untuk Peta
            $sitesForMap = Site::whereNotNull('latitude')->whereNotNull('longitude')->get();

            // Data untuk Grafik Tren Bulanan (6 bulan terakhir)
            $monthlyIncidentTrend = Incident::select(
                    DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')->orderBy('month', 'asc')
                ->get();

            // Data untuk Top 5 Site Bermasalah
            $topProblematicSites = Site::withCount('incidents')
                ->orderBy('incidents_count', 'desc')
                ->take(5)
                ->get();
        }
        return view('dashboard', compact('stats', 'sitesForMap', 'monthlyIncidentTrend', 'topProblematicSites'));
    }
}

