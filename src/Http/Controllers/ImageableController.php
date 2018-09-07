<?php

namespace Lloricode\LaravelImageable\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelImageable\Models\ImageFile;
use Illuminate\Support\Facades\Config;

class ImageableController extends Controller
{
    public function show(ImageFile $imageFile)
    {
        $storage = Config::get("filesystems.disks.{$imageFile->disk}.root");

        return response()->file($storage . '/' . $imageFile->path, [
            'Content-Type' => $imageFile->content_type,
        ]);
    }
}
