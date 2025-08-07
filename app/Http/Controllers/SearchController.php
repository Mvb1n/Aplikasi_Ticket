<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dari URL
        $query = $request->input('query');
        $incidents = collect(); // Buat koleksi kosong sebagai default

        // Lakukan pencarian hanya jika ada kata kunci
        if ($query) {
            // Cari di tabel Incidents berdasarkan judul atau lokasi
            $incidents = Incident::with(['user', 'site']) // Eager load relasi
                                 ->where('title', 'LIKE', "%{$query}%")
                                 ->orWhere('location', 'LIKE', "%{$query}%")
                                 ->orWhereHas('assets', function($q) use ($query) {
                                     $q->where('name', 'LIKE', "%{$query}%")
                                       ->orWhere('serial_number', 'LIKE', "%{$query}%");
                                 })
                                 ->latest()
                                 ->paginate(10);
        }

        // Kirim kata kunci dan hasil pencarian ke view
        return view('search.index', compact('query', 'incidents'));
    }
}
