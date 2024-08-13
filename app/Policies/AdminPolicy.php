<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Admin $admin): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Admin $admin): bool
    {
    }

    public function delete(User $user, Admin $admin): bool
    {
    }

    public function restore(User $user, Admin $admin): bool
    {
    }

    public function forceDelete(User $user, Admin $admin): bool
    {
    }
}
