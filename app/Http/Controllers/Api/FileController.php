<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;
use App\Models\Address;
use App\Models\File;
use App\Models\Advertising;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Configuration;

use Gate;

class FileController extends Controller
{
    /**
     * Subir archivo y asociarlo a Pet o Address
     */
    public function upload(Request $request)
    {
        Gate::authorize('create', File::class);
        try {
            $request->validate([
                'file' => 'required|file',
                'model' => 'required|string|in:pet,address,advertising,promotion,product,configuration',
                'id' => 'required|integer',
            ]);

            // Resolver el modelo dinámicamente
            $modelClass = match ($request->model) {
                'pet' => Pet::class,
                'address' => Address::class,
                'advertising' => Advertising::class,
                'promotion' => Promotion::class,
                'product' => Product::class,
                'configuration' => Configuration::class
            };

            $model = $modelClass::findOrFail($request->id);

            // Guardar archivo usando el trait HasFiles
            $file = $model->addFile($request->file('file'));

            return response()->json([
                'message' => 'Archivo subido correctamente',
                'data' => $file
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al subir archivo',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Listar archivos de Pet o Address
     */
    public function list(Request $request)
    {
        Gate::authorize('viewAny', File::class);
        try {
            $request->validate([
                'model' => 'required|string|in:pet,address,advertising,promotion,product,configuration',
                'id' => 'required|integer',
            ]);

            $modelClass = match ($request->model) {
                'pet' => Pet::class,
                'address' => Address::class,
                'advertising' => Advertising::class,
                'promotion' => Promotion::class,
                'product' => Product::class,
                'configuration' => Configuration::class
            };

            $model = $modelClass::findOrFail($request->id);

            return response()->json([
                'data' => $model->files
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener archivos',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Mostrar archivo específico
     */
    public function show(File $file)
    {
        Gate::authorize('view', $file);
        return response()->json($file, 200);
    }

    /**
     * Eliminar archivo
     */
    public function destroy(File $file)
    {
        Gate::authorize('delete', $file);
        try {
            $path = 'files/' . $file->name . '.' . $file->extension;

            $protectedNames = ['default', 'service', 'favicon', 'specialty', 'testimonial', 'team', 'logo', 'banner'];

            if (!in_array($file->name, $protectedNames) && Storage::exists($path)) {
                Storage::delete($path);
            }

            $file->delete();

            return response()->json([
                'message' => 'Archivo eliminado correctamente'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al eliminar archivo',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
