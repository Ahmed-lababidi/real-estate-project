<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    // public function upload(
    //     UploadedFile $file,
    //     string $folder,
    //     ?string $oldPath = null,
    //     string $disk = 'public'
    // ): string {
    //     if ($oldPath) {
    //         $this->delete($oldPath, $disk);
    //     }

    //     $filename = Str::uuid() . '.webp';
    //     $path = $folder . '/' . $filename;

    //     $image = Image::read($file)
    //         ->scaleDown(width: 1600)
    //         ->toWebp(85);

    //     Storage::disk($disk)->put($path, (string) $image);

    //     return $path;
    // }


    public function upload(
        UploadedFile $file,
        string $directory,
        ?string $oldPath = null,
        string $disk = 'public'
    ): string {
        if ($oldPath) {
            $this->delete($oldPath, $disk);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }
    public function delete(?string $path, string $disk = 'public'): bool
    {
        if (! $path) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
