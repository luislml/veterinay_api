<?php

namespace App\Policies;

use App\Models\User;
use App\Models\File;

class FilePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any files');
    }

    public function view(User $user, File $file)
    {
        return $user->hasPermissionTo('view files');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create files');
    }

    public function update(User $user, File $file)
    {
        return $user->hasPermissionTo('update files');
    }

    public function delete(User $user, File $file)
    {
        return $user->hasPermissionTo('delete files');
    }

    public function restore(User $user, File $file)
    {
        return false;
    }

    public function forceDelete(User $user, File $file)
    {
        return false;
    }
}
