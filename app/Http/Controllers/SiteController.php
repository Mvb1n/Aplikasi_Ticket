<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Menampilkan halaman daftar semua site.
     */
    public function index()
    {
        $this->authorize('viewAny', Site::class);
        // Ambil data site beserta jumlah aset berdasarkan statusnya.
        // Ini adalah cara yang efisien untuk menghindari query N+1.
        $sites = Site::withCount([
            'assets',
            'assets as assets_in_use_count' => function ($query) {
                $query->where('status', 'In Use');
            },
            'assets as assets_stolen_lost_count' => function ($query) {
                $query->where('status', 'Stolen/Lost');
            },
            'assets as assets_in_repair_count' => function ($query) {
                $query->where('status', 'In Repair');
            }
        ])->get();
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        $this->authorize('create', Site::class);
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Site::class);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:sites',
            'location_code' => 'required|string|max:10|unique:sites',
            'address' => 'nullable|string',
        ]);
        Site::create($validatedData);
        return redirect()->route('sites.index')->with('success', 'Site baru berhasil ditambahkan.');
    }

public function show(Site $site)
{
    $this->authorize('view', $site);
    // Kelompokkan aset berdasarkan kategori untuk ditampilkan di view
    $assetsByCategory = $site->assets()->latest()->get()->groupBy('category');
    return view('sites.show', compact('site', 'assetsByCategory'));
}

    public function edit(Site $site)
    {
        $this->authorize('update', $site);
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $this->authorize('update', $site);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:sites,name,' . $site->id,
            'location_code' => 'required|string|max:10|unique:sites,location_code,' . $site->id,
            'address' => 'nullable|string',
        ]);
        $site->update($validatedData);
        return redirect()->route('sites.index')->with('success', 'Data site berhasil diperbarui.');
    }

    public function destroy(Site $site)
    {
        $this->authorize('delete', $site);
        $site->delete();
        return redirect()->route('sites.index')->with('success', 'Site berhasil dihapus.');
    }

    /**
     * Menampilkan daftar aset yang difilter berdasarkan kategori dan status.
     */
    public function showAssetList(Site $site, $category, $status)
    {
        // Ganti karakter strip '-' di URL menjadi spasi atau slash '/' agar sesuai dengan database
        if ($status === 'Stolen-Lost') {
            $dbStatus = 'Stolen/Lost';
        } else {
            $dbStatus = str_replace('-', ' ', $status);
        }

        $assets = $site->assets()
                    ->where('category', $category)
                    ->where('status', $dbStatus)
                    ->get();

        // Mengirim SEMUA variabel yang dibutuhkan ke view
        return view('sites.assets.index', compact('site', 'category', 'status', 'assets'));
    }
}