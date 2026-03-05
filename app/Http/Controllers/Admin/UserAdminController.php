<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\users\UserResource;
use App\Http\Resources\users\UserCollection;
use DB;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $role = $request->query('role');           // Ej: admin, veterinary, user
        $search = $request->query('search');       // Ej: texto de búsqueda
        $paginate = $request->query('paginate', true); // Por defecto true
        $perPage = $request->query('per_page', 10);     // Items por página

        $query = User::with(['roles']);


        // Filtrar por rol
        if ($role) {
            $query->role($role);
        }

        // Filtrar por búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Retornar con o sin paginación
        if ($paginate === 'false') {
            $users = $query->get();
            return new UserCollection($users);
        }

        $users = $query->paginate($perPage);
        return new UserCollection($users);
}

    public function store(UserAdminRequest $request)
    {
        Gate::authorize('create', User::class);
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Hashear contraseña
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            // Asignar rol (si viene en la request, ej: 'admin', 'veterinary', 'user')
            if ($request->has('role')) {
                $user->assignRole($request->role);
            }
            DB::commit();
            return new UserResource($user);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line'  => $th->getLine(),
                'file'  => $th->getFile(),
            ], 500);
        }
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return response()->json([
            'user' => new UserResource($user),
            'roles' => $user->getRoleNames(),
            'veterinaries' => $user->veterinaries,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function update(UserAdminRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Solo actualizar password si viene en el request
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            DB::commit();
            return new UserResource($user);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'line'  => $th->getLine(),
                'file'  => $th->getFile(),
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->json(null, 204);
    }
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $user);

        $user->restore();

        return response()->json(['message' => 'Usuario restaurado correctamente.']);
    }
    public function me(Request $request)
    {
        $user = $request->user(); // o Auth::user()

        // Puedes usar UserResource y agregar roles y permisos
        return response()->json([
            'user' => new UserResource($user),
            'roles' => $user->getRoleNames(),
            'veterinaries' => $user->veterinaries,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }
}
