<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Lloricode\LaravelImageable\Uploader;
use Lloricode\LaravelImageable\Getter;
use Lloricode\LaravelImageable\Models\Image;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

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

    public function getCachePrefix():string
    {
        $names = [];
        foreach (explode('\\', get_class($this)) as $exploded) {
            $names[] = str_replace('-', '_', kebab_case($exploded));
        }
        return Config::get('imageable.cache.prefix') . '_' . implode('_', $names) . '_' . $this->id;
    }
}
