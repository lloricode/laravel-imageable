<?php

namespace Lloricode\LaravelImageable\Tests;

use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait Functions
{
    protected function generateFakeFile(int $count = 1, $ext = 'jpg', $size  = 1234)
    {
        if ($count === 1) {
            return UploadedFile::fake()->image("avatar.$ext")->size($size);
        }

        $files = [];

        for ($i = 0; $i < $count; $i++) {
            $files[] = UploadedFile::fake()->image("avatar$i.$ext")->size($size);
        }

        return $files;
    }

    protected function assertStorage(Image $image)
    {
        foreach ($image->imageFiles as $imageFile) {
            Storage::disk($imageFile->storage_driver)->assertExists($imageFile->path);
        }
    }
}
