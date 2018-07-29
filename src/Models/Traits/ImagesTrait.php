<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Models\HelperClass\Uploader;

trait ImageableTrait
{
    public function images($images):Uploader
    {
        return new Uploader($this, $images);
    }
}
