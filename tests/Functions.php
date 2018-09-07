<?php

namespace Lloricode\LaravelImageable\Tests;

use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

trait Functions
{
    protected function generateFakeFile(int $count = 1, $ext = 'jpg', $width = null, int $height = null, $size  = 1234)
    {
        if ($count === 1) {
            if (!is_null($width) && !is_null($height)) {
                return UploadedFile::fake()->image("avatar.$ext", $width, $height)->size($size);
            }
            return UploadedFile::fake()->image("avatar.$ext")->size($size);
        }

        $files = [];

        for ($i = 0; $i < $count; $i++) {
            if (!is_null($width) && !is_null($height) && is_array($width)) {
                $files[] = UploadedFile::fake()->image("avatar$i.$ext", $width[$i]['w'], $height[$i]['h'])->size($size);
            } else {
                $files[] = UploadedFile::fake()->image("avatar$i.$ext")->size($size);
            }
        }

        return $files;
    }

    // protected function assertStorage(Image $image =null)
    // {
    //     // foreach ($image->imageFiles as $imageFile) {
    //         // $storage = Config::get("filesystems.disks.{$imageFile->disk}.root");
    //         // $this->assertFileExists($storage . '/' . $imageFile->path, 'Image file Not found');
    //     // }
    // }
}
