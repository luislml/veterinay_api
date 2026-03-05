<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentVeterinary;
use Illuminate\Http\Request;
use App\Http\Requests\ContentVeterinaryRequest;
use App\Http\Resources\ContentVeterinaryResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ContentVeterinaryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ContentVeterinary::class);

        $query = ContentVeterinary::query();

        if ($request->has('veterinary_id')) {
            $query->where('veterinary_id', $request->veterinary_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $contents = $query->latest()->get();
        return ContentVeterinaryResource::collection($contents);
    }

    public function store(ContentVeterinaryRequest $request)
    {
        Gate::authorize('create', ContentVeterinary::class);

        DB::beginTransaction();
        try {
            $contentVeterinary = ContentVeterinary::create($request->validated());

            if ($request->hasFile('image')) {
                $contentVeterinary->addFile($request->file('image'));
            }

            DB::commit();
            return new ContentVeterinaryResource($contentVeterinary->load('files'));

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
        $contentVeterinary = ContentVeterinary::findOrFail($id);
        Gate::authorize('view', $contentVeterinary);
        return new ContentVeterinaryResource($contentVeterinary);
    }

    public function update(ContentVeterinaryRequest $request, $id)
    {
        $contentVeterinary = ContentVeterinary::findOrFail($id);
        Gate::authorize('update', $contentVeterinary);

        DB::beginTransaction();
        try {
            $contentVeterinary->update($request->validated());

            if ($request->hasFile('image')) {
                // Remove all existing files
                foreach ($contentVeterinary->files as $file) {
                    $path = 'files/' . $file->name . '.' . $file->extension;

                    // Solo borrar el archivo físico si NO es el default
                    $protectedNames = ['default', 'banner', 'service', 'specialty', 'testimonial'];
                    if (!in_array($file->name, $protectedNames) && Storage::exists($path)) {
                        Storage::delete($path);
                    }

                    $file->delete();
                }

                // Add new file
                $contentVeterinary->addFile($request->file('image'));
            }

            DB::commit();
            return new ContentVeterinaryResource($contentVeterinary->load('files'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $contentVeterinary = ContentVeterinary::findOrFail($id);
        Gate::authorize('delete', $contentVeterinary);

        // Delete associated files
        foreach ($contentVeterinary->files as $file) {
            $path = 'files/' . $file->name . '.' . $file->extension;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            $file->delete();
        }

        $contentVeterinary->delete();
        return response()->json(null, 204);
    }
}
