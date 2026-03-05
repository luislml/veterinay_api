<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImageVeterinary;
use Illuminate\Http\Request;
use App\Http\Requests\ImageVeterinaryRequest;
use App\Http\Resources\ImageVeterinaryResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ImageVeterinaryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ImageVeterinary::class);

        $query = ImageVeterinary::query();

        if ($request->has('veterinary_id')) {
            $query->where('veterinary_id', $request->veterinary_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $images = $query->latest()->get();
        return ImageVeterinaryResource::collection($images);
    }

    public function store(ImageVeterinaryRequest $request)
    {
        Gate::authorize('create', ImageVeterinary::class);

        DB::beginTransaction();
        try {
            $imageVeterinary = ImageVeterinary::create($request->validated());

            if ($request->hasFile('image')) {
                $imageVeterinary->addFile($request->file('image'));
            }

            DB::commit();
            return new ImageVeterinaryResource($imageVeterinary->load('files'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function show($id)
    {
        $imageVeterinary = ImageVeterinary::findOrFail($id);
        Gate::authorize('view', $imageVeterinary);
        return new ImageVeterinaryResource($imageVeterinary);
    }

    public function update(ImageVeterinaryRequest $request, $id)
    {
        $imageVeterinary = ImageVeterinary::findOrFail($id);
        Gate::authorize('update', $imageVeterinary);

        DB::beginTransaction();
        try {
            $imageVeterinary->update($request->validated());

            if ($request->hasFile('image')) {
                // Remove all existing files
                foreach ($imageVeterinary->files as $file) {
                    $path = 'files/' . $file->name . '.' . $file->extension;

                    // Solo borrar el archivo físico si NO es el default
                    $protectedNames = ['default', 'team', 'logo', 'testimonial'];
                    if (!in_array($file->name, $protectedNames) && Storage::exists($path)) {
                        Storage::delete($path);
                    }

                    $file->delete();
                }

                // Add new file
                $imageVeterinary->addFile($request->file('image'));
            }

            DB::commit();
            return new ImageVeterinaryResource($imageVeterinary->load('files'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $imageVeterinary = ImageVeterinary::findOrFail($id);
        Gate::authorize('delete', $imageVeterinary);

        // Delete associated files
        foreach ($imageVeterinary->files as $file) {
            $path = 'files/' . $file->name . '.' . $file->extension;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            $file->delete();
        }

        $imageVeterinary->delete();
        return response()->json(null, 204);
    }
}
