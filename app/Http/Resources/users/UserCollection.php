<?php

namespace App\Http\Resources\users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id'        => $user->id,
                'name'      => $user->name,
                'last_name' => $user->last_name,
                'phone'     => $user->phone,
                'email'     => $user->email,
                'roles'     => $user->getRoleNames(), // <-- AHORA SÍ 🎉
            ];
        });
    }
}
