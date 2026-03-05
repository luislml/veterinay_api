<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Actualiza los datos del usuario autenticado.
     */
    public function update(Request $request)
    {
        if (! $request->user()->hasRole('admin')) {
        return response()->json([
            'message' => 'No tienes acceso'
        ], 403);
    }
        $user = $request->user();

        // Validación
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'required', 'min:8', 'confirmed'],
        ]);

        // Actualizar nombre si se envía
        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        // Actualizar email si se envía
        if ($request->has('email')) {
            $user->email = $validated['email'];
        }

        // Actualizar contraseña si se envía
        if ($request->has('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user->only(['id', 'name', 'email']),
        ]);
    }
}