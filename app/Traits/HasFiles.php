<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

trait HasFiles
{
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function addFile($uploaded)
    {
        if (!$this->exists) {
            throw new \Exception("El modelo debe estar guardado antes de agregar archivos.");
        }

        $extension = $uploaded->getClientOriginalExtension();
        $type = $uploaded->getClientMimeType();

        // Generar nombre automático img_2025_1, img_2025_2...
        $lastFile = File::whereYear('created_at', now()->year)->latest('id')->first();
        $number = $lastFile ? ($lastFile->id + 1) : 1;
        $name = 'img_' . now()->year . '_' . $number;

        // Guardar registro en la base de datos
        $file = $this->files()->create([
            'name' => $name,
            'type' => $type,
            'extension' => $extension,
        ]);

        if (!$file) {
            throw new \Exception("No se pudo crear el registro del archivo en la base de datos.");
        }

        // Carpeta y ruta del archivo
        $filePath = 'files/' . $file->name . '.' . $file->extension;

        // Guardar el archivo usando Laravel (funciona mejor en Docker)
        $uploaded->storeAs('files', $file->name . '.' . $file->extension, 'public');

        // Optimizar si es imagen
        if (str_starts_with($type, 'image/')) {
            $fullPath = storage_path('app/public/' . $filePath);

            // Redimensionar imagen si es muy grande (máximo 1920px)
            $this->resizeImage($fullPath, 1200);

            // Optimizar con Spatie
            ImageOptimizer::optimize($fullPath);
        }

        return $file;
    }

    /**
     * Redimensiona una imagen si supera el tamaño máximo especificado
     * manteniendo la proporción de aspecto.
     *
     * @param string $path Ruta completa al archivo de imagen
     * @param int $maxSize Tamaño máximo en píxeles (ancho o alto)
     * @return void
     */
    protected function resizeImage(string $path, int $maxSize = 1920): void
    {
        if (!file_exists($path)) {
            return;
        }

        // Obtener información de la imagen
        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            return;
        }

        [$width, $height, $type] = $imageInfo;

        // Si la imagen ya es pequeña, no hacer nada
        if ($width <= $maxSize && $height <= $maxSize) {
            return;
        }

        // Calcular nuevas dimensiones manteniendo proporción
        if ($width > $height) {
            $newWidth = $maxSize;
            $newHeight = (int) ($height * ($maxSize / $width));
        } else {
            $newHeight = $maxSize;
            $newWidth = (int) ($width * ($maxSize / $height));
        }

        // Crear imagen desde el archivo según el tipo
        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };

        if (!$source) {
            return;
        }

        // Crear nueva imagen redimensionada
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG y GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Redimensionar
        imagecopyresampled(
            $destination,
            $source,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        // Guardar imagen redimensionada (calidad 75 para mejor compresión)
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($destination, $path, 75),
            IMAGETYPE_PNG => imagepng($destination, $path, 7), // 0-9, donde 9 es máxima compresión
            IMAGETYPE_GIF => imagegif($destination, $path),
            IMAGETYPE_WEBP => imagewebp($destination, $path, 75),
            default => null,
        };

        // Liberar memoria
        imagedestroy($source);
        imagedestroy($destination);
    }
}

