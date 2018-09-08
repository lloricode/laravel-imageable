<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Uploader;
use Lloricode\LaravelImageable\Getter;
use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Support\Collection;

trait ImageableTrait
{
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function uploads(array $images):Uploader
    {
        return new Uploader($this, $images);
    }

    public function getImages(string $name = null, string $category = null, string $group = null):Collection
    {
        $getter = new Getter($this);
        $getter->setName($name);
        $getter->setGroup($group);
        $getter->setCategory($category);

        return $getter->result();
    }
}
