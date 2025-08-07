<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function index()
    {
        // Ambil semua token milik pengguna yang sedang login
        $tokens = Auth::user()->tokens;
        return view('api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        // Buat token baru dengan izin penuh (bisa disesuaikan)
        $token = $request->user()->createToken($request->name);

        // Simpan token di session agar bisa ditampilkan sekali saja
        return back()->with('token', $token->plainTextToken);
    }
}
