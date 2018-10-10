<?php

namespace Lloricode\LaravelImageable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Getter
{
    private $_model;
    private $_sizeName;
    private $_group;
    private $_category;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function setName(string $sizeName = null)
    {
        $this->_sizeName = $sizeName;
    }

    public function setGroup(string $group = null)
    {
        $this->_group = $group;
    }

    public function setCategory(string $category = null)
    {
        $this->_category = $category;
    }

    public function result():Collection
    {
        $return = collect([]);
        
        $imageFiles = $this->_getImage();

        if (empty($imageFiles)) {
            return $return;
        }

        foreach ($imageFiles as $imageFile) {
            $disk = Config::get("filesystems.disks.{$imageFile->disk}");

            $data = new \stdClass;

            $data->size_name = $imageFile->size_name;
            $data->category = $imageFile->category;
            $data->group = $imageFile->group;
            $data->client_original_name = $imageFile->client_original_name;
            $data->slug = $imageFile->slug;
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

    private function _getImage()
    {
        $images = $this
            ->_model
            ->images()
            ->select(
                'slug',
                'disk',
                'path',
                'category',
                'group',
                'size_name',
                'client_original_name'
            );
        
        $cacheName = '';
        foreach (explode('\\', get_class($this->_model)) as $exploded) {
            $cacheName .= '-' . str_replace('-', '_', kebab_case($exploded));
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

    private function _multiImplode($glue, $array)
    {
        $ret = '';
        foreach ($array as $item) {
            if (is_array($item)) {
                $ret .= $this->_multiImplode($glue, $item) . $glue;
            } else {
                $ret .= $item . $glue;
            }
        }
        $ret = substr($ret, 0, 0-strlen($glue));
    
        return $ret;
    }

    // private function _cacheName()
    // {
    //     $cacheName = '';
    //     if (!is_null($this->_sizeName)) {
    //         $cacheName .= strtolower($this->_sizeName) . '_';
    //     }
    //     if (!is_null($this->_category)) {
    //         $cacheName .= strtolower($this->_category) . '_';
    //     }
    //     if (!is_null($this->_group)) {
    //         $cacheName .= strtolower($this->_group) . '_';
    //     }

    //     return $this->_model->getCachePrefix() . '_' . trim($cacheName, '_');
    // }
}
