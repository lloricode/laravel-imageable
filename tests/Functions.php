<?php

namespace Lloricode\LaravelImageable\Tests;

use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Http\UploadedFile;
use Storage;

trait Functions
{
    protected function generateFakeFile(int $count = 1, $ext = 'jpg')
    {
        if ($count === 1) {
            return UploadedFile::fake()->image("avatar.$ext");
        }

        $files = [];

        for ($i = 0; $i < $count; $i++) {
            $files[] = UploadedFile::fake()->image("avatar$i.$ext");
        }

        return $files;
    }

    protected function assertStorage(Image $image)
    {
        foreach ($image->imageFiles as $imageFile) {
            if ($imageFile->is_storage) {
                Storage::disk('local')->assertExists(str_replace('app/', '', $imageFile->path));
            } else {
                $this->assertTrue(file_exists(public_path($imageFile->path)));
            }
        }
    }
}
