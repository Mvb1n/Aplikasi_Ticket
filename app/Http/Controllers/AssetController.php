<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Menampilkan halaman daftar aset.
     */
    public function index()
    {
        $this->authorize('viewAny', Asset::class);
        $assets = Asset::latest()->paginate(15);
        return view('assets.index', compact('assets'));
    }

    /**
     * Menampilkan form untuk menambah aset baru.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Asset::class);
        // Ambil site_id dari URL jika ada, untuk pre-select dropdown
        $site_id = $request->query('site_id');
        $sites = Site::all();
        return view('assets.create', compact('sites', 'site_id'));
    }

    /**
     * Menyimpan data dari form tambah aset baru.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number',
            'category' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
            'status' => 'required|in:In Use,In Repair',
        ]);
        Asset::create($validatedData);
        return redirect()->route('sites.show', $validatedData['site_id'])->with('success', 'Aset baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu aset.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Menampilkan form untuk mengedit aset.
     */
    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);
        // Ambil semua site untuk ditampilkan di dropdown
        $sites = Site::orderBy('name')->get();
        return view('assets.edit', compact('asset', 'sites'));
    }

    /**
     * Menyimpan perubahan dari form edit aset.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number,' . $asset->id,
            'category' => 'required|string',
            'site_id' => 'required|exists:sites,id', // Validasi site_id
            'status' => 'required|in:In Use,In Repair,Stolen/Lost,Decommissioned',
        ]);
        $asset->update($validatedData);
        return redirect()->route('sites.show', $asset->site_id)->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Menghapus data aset.
     */
    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        $site_id = $asset->site_id; // Simpan site_id sebelum dihapus
        $asset->delete();
        return redirect()->route('sites.show', $site_id)->with('success', 'Aset berhasil dihapus.');
    }

    public function getAssetsBySite(Site $site)
    {
        // Ambil hanya aset yang statusnya "In Use" di site yang dipilih
        $assets = $site->assets()->where('status', 'In Use')->get(['id', 'name', 'serial_number']);
        // Kembalikan data dalam format JSON
        return response()->json($assets);
    }
}