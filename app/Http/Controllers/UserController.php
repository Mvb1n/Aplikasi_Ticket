<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
            ->orderByRaw("
                CASE
                    WHEN exists(select 1 from role_user ru inner join roles r on ru.role_id = r.id where ru.user_id = users.id and r.name = 'admin') THEN 1
                    WHEN exists(select 1 from role_user ru inner join roles r on ru.role_id = r.id where ru.user_id = users.id and r.name = 'security') THEN 2
                    ELSE 3
                END
            ")
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Ambil semua peran yang ada di database untuk ditampilkan sebagai pilihan
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validasi diubah untuk menerima satu nilai, bukan array
        $request->validate(['role' => 'required|exists:roles,id',]);

        // Method sync() sangat efisien untuk ini.
        // Ia akan men-sinkronkan peran pengguna hanya dengan ID yang baru.
        $user->roles()->sync($request->role);
        return redirect()->route('users.index')->with('success', 'Peran pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
{
    // Otorisasi: Periksa apakah pengguna yang login diizinkan menghapus user ini
    $this->authorize('delete', $user);
    // Hapus pengguna dari database
    $user->delete();
    // Arahkan kembali ke halaman daftar dengan pesan sukses
    return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
}
}
