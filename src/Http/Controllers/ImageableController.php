<?php

namespace Lloricode\LaravelImageable\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Lloricode\LaravelImageable\Models\Image;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImageableController
 *
 * @package Lloricode\LaravelImageable\Http\Controllers
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class ImageableController extends Controller
{
    /**
     * @param \Lloricode\LaravelImageable\Models\Image $image
     * @return mixed
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function show(Image $image)
    {
        $storage = Config::get("filesystems.disks.{$image->disk}.root");

        return response()->file($storage.'/'.$image->path, [
            'Content-Type' => $image->content_type,
        ]);
    }

    /**
     * @param \Lloricode\LaravelImageable\Models\Image $image
     * @return \Illuminate\Http\JsonResponse
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function delete(Image $image)
    {
        $imageable = $image->imageable;

        $imageable->deleteImages(null, $image->category, $image->group);

        if (Config::get('imageable.cache.enable') === true) {
            Cache::tags($imageable->getCachePrefix())->flush();
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
