<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Uploader;
use Lloricode\LaravelImageable\Getter;
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

    public function getImages(string $name = null, string $group = null, string $category = null)
    {
        $getter = new Getter($this);
        $getter->setName($name);
        // $getter->setGroup($group);
        // $getter->setCategory($category);

        return $getter->result();
    }
}
