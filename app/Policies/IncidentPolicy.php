<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IncidentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Incident $incident): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Siapa yang boleh meng-update (status, investigasi).
     */
    public function update(User $user, Incident $incident): bool
    {
        // Izinkan jika pengguna adalah admin ATAU security
        return $user->hasRole('admin') || $user->hasRole('security');
    }

    /**
     * Siapa yang boleh meng-edit (detail inti laporan).
     * Kita buat aturan ini lebih ketat.
     */
    public function edit(User $user, Incident $incident): bool
    {
        // Hanya admin yang boleh mengedit detail inti
        return $user->hasRole('admin');
    }

    /**
     * Siapa yang boleh menghapus laporan.
     */
    public function delete(User $user, Incident $incident): bool
    {
        // Hanya admin yang boleh menghapus
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Incident $incident): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Incident $incident): bool
    {
        return false;
    }
}
