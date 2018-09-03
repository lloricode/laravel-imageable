<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Uploader;

trait ImageableTrait
{
    public function images($images):Uploader
    {
        return new Uploader($this, $images);
    }
}
