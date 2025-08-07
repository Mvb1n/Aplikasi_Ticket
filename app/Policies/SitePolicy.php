<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    // Siapa yang boleh melihat daftar site
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    // Siapa yang boleh melihat detail satu site
    public function view(User $user, Site $site): bool
    {
        return $user->hasRole('admin');
    }

    // Siapa yang boleh membuat site baru
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    // Siapa yang boleh mengupdate site
    public function update(User $user, Site $site): bool
    {
        return $user->hasRole('admin');
    }

    // Siapa yang boleh menghapus site
    public function delete(User $user, Site $site): bool
    {
        return $user->hasRole('admin');
    }
}