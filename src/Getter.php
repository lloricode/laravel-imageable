<?php

namespace Lloricode\LaravelImageable;

use Cache;
use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;

/**
 * Class Getter
 *
 * @package Lloricode\LaravelImageable
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class Getter
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $_model;

    /**
     * @var
     */
    private $_sizeName;

    /**
     * @var
     */
    private $_group;

    /**
     * @var
     */
    private $_category;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function setModel(Model $model)
    {
        $this->_model = $model;
    }

    /**
     * @param string|null $sizeName
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function setName(string $sizeName = null)
    {
        $this->_sizeName = $sizeName;
    }

    /**
     * @param string|null $group
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function setGroup(string $group = null)
    {
        $this->_group = $group;
    }

    /**
     * @param string|null $category
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function setCategory(string $category = null)
    {
        $this->_category = $category;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function result(): Collection
    {
        $return = collect([]);

        $imageFiles = $this->_getImage();

        if (empty($imageFiles)) {
            return $return;
        }

        foreach ($imageFiles as $imageFile) {
            $disk = Config::get("filesystems.disks.{$imageFile->disk}");

            $data = new stdClass();

            $data->size_name = $imageFile->size_name;
            $data->category = $imageFile->category;
            $data->group = $imageFile->group;
            $data->client_original_name = $imageFile->client_original_name;
            $data->slug = $imageFile->slug;
            $data->order = $imageFile->order;
            $data->source_delete = route('imageable.web.delete', $imageFile);
            $data->source = null;

            // check disk if visibility is public
            if (isset($disk['visibility'])) {
                if ($disk['visibility'] == 'public') {
                    $data->source = $disk['url'] . '/' . $imageFile->path;
                }
            }

            if (is_null($data->source)) {
                $data->source = route('imageable.web.show', $imageFile);
            }

            $return->push($data);
        }

        return $return;
    }

    /**
     * @return mixed
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _getImage()
    {
        $images = $this->_model->images()->select('slug', 'disk', 'path', 'category', 'group', 'size_name', 'order',
            'client_original_name');

        $cacheName = '';
        foreach (explode('\\', get_class($this->_model)) as $exploded) {
            $cacheName .= '-'.str_replace('-', '_', Str::kebab($exploded));
        }

        $cacheName .= '-' . $this->_model->id;

        if (!is_null($this->_sizeName)) {
            $images = $images->where('size_name', $this->_sizeName);
            $cacheName .= '-' . $this->_sizeName;
        }
        if (!is_null($this->_category)) {
            $images = $images->where('category', $this->_category);
            $cacheName .= '-' . $this->_category;
        }
        if (!is_null($this->_group)) {
            $images = $images->where('group', $this->_group);
            $cacheName .= '-' . $this->_group;
        }

        $cacheName = str_replace('_', '-', $cacheName);

        if (Config::get('imageable.cache.enable') === true) {
            if (Cache::tags($this->_model->getCachePrefix())->has($cacheName)) {
                return Cache::tags($this->_model->getCachePrefix())->get($cacheName);
            }

            $data = $images->get();

            Cache::tags($this->_model->getCachePrefix())->forever($cacheName, $data);

            return $data;
        }

        return $images->get();
    }
}
