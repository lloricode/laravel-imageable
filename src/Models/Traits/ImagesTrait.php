<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Lloricode\LaravelImageable\Getter;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Uploader;

/**
 * Trait ImageableTrait
 *
 * @package Lloricode\LaravelImageable\Models\Traits
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
trait ImageableTrait
{
    /**
     * @param array $images
     * @return \Lloricode\LaravelImageable\Uploader
     * @throws \Throwable
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function uploads(array $images): Uploader
    {
        return new Uploader($this, $images);
    }

    /**
     * @return string
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function getCachePrefix(): string
    {
        $names = [];
        foreach (explode('\\', get_class($this)) as $exploded) {
            $names[] = str_replace('-', '_', kebab_case($exploded));
        }

        return Config::get('imageable.cache.prefix').'_'.implode('_', $names).'_'.$this->id.'_queries';
    }

    /**
     * @param string|null $name
     * @param string|null $category
     * @param string|null $group
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function deleteImages(string $name = null, string $category = null, string $group = null)
    {
        DB::transaction(function () use ($name, $category, $group) {
            $this->getImages($name, $category, $group)->map(function ($image) {
                $image = $this->images()->where('slug', $image->slug)->first();

                if (Config::get('imageable.cache.enable') === true) {
                    Cache::tags($image->imageable->getCachePrefix())->flush();
                }
                $image->delete();
            });
        });
    }

    /**
     * @param string|null $name
     * @param string|null $category
     * @param string|null $group
     * @return \Illuminate\Support\Collection
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function getImages(string $name = null, string $category = null, string $group = null): Collection
    {
        $getter = new Getter($this);
        $getter->setName($name);
        $getter->setGroup($group);
        $getter->setCategory($category);

        return $getter->result();
    }

    /**
     * @return mixed
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
