<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // --- LOGIKA PENETAPAN PERAN DIMULAI DI SINI ---

        // 1. Cari peran 'staff' di database.
        $staffRole = Role::where('name', 'staff')->first();

        // 2. Jika peran 'staff' ditemukan, tautkan ke pengguna baru.
        if ($staffRole) {
            $user->roles()->attach($staffRole);
        }
        // Jika tidak ditemukan, pengguna baru tidak akan memiliki peran apa pun (aman).
    
        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
