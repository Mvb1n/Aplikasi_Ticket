<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Siapa yang boleh melihat halaman daftar/manajemen pengguna & impor)
     */
    public function viewAny(User $user): bool
    {
        // Hanya admin yang boleh melihat
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     * (Siapa yang boleh membuat/mengimpor data baru)
     */
    public function create(User $user): bool
    {
        // Hanya admin yang boleh membuat
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     * (Siapa yang boleh menghapus pengguna lain)
     */
    public function delete(User $user, User $model): bool
    {
        // Aturan:
        // 1. Pengguna harus memiliki peran 'admin'.
        // 2. Pengguna tidak boleh menghapus akunnya sendiri.
        return $user->hasRole('admin') && $user->id !== $model->id;
    }
}
