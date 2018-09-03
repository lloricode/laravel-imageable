<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Uploader;
use Lloricode\LaravelImageable\Models\Image;

trait ImageableTrait
{
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function uploads($images):Uploader
    {
        return new Uploader($this, $images);
    }
}
