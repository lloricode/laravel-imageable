<?php

namespace Lloricode\LaravelImageable\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Support\Facades\Config;

class ImageableController extends Controller
{
    public function show(Image $image)
    {
        $storage = Config::get("filesystems.disks.{$image->disk}.root");

        return response()->file($storage . '/' . $image->path, [
            'Content-Type' => $image->content_type,
        ]);
    }
}
