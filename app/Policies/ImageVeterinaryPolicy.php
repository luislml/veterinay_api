<?php

namespace App\Policies;

use App\Models\ImageVeterinary;
use App\Models\User;

class ImageVeterinaryPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view any image-veterinaries');
    }

    public function view(User $user, ImageVeterinary $imageVeterinary)
    {
        return $user->hasPermissionTo('view image-veterinaries');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create image-veterinaries');
    }

    public function update(User $user, ImageVeterinary $imageVeterinary)
    {
        return $user->hasPermissionTo('update image-veterinaries');
    }

    public function delete(User $user, ImageVeterinary $imageVeterinary)
    {
        return $user->hasPermissionTo('delete image-veterinaries');
    }

    public function restore(User $user, ImageVeterinary $imageVeterinary)
    {
        return false;
    }

    public function forceDelete(User $user, ImageVeterinary $imageVeterinary)
    {
        return false;
    }
}
