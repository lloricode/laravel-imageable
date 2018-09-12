<?php

namespace Lloricode\LaravelImageable\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use DB;

class ImageableController extends Controller
{
    public function show(Image $image)
    {
        $storage = Config::get("filesystems.disks.{$image->disk}.root");

        return response()->file($storage . '/' . $image->path, [
            'Content-Type' => $image->content_type,
        ]);
    }

    public function delete(Image $image)
    {
        $image->imageable->deleteImages(null, $image->category, $image->group);

        if (Config::get('imageable.cache.enable') === true) {
            Image::flushCache($image->imageable->getCachePrefix());
        }
        
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
