<?php

namespace Lloricode\LaravelImageable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;

class Getter
{
    private $_model;
    private $_name;
    private $_group;
    private $_category;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function setName(string $name)
    {
        $this->_name = $name;
    }

    public function setGroup(string $group)
    {
        $this->_group = $group;
    }

    public function setCategory(string $category)
    {
        $this->_category = $category;
    }

    public function result():Collection
    {
        $return = collect([]);

        $imageFiles = $this->_getImage();

        if (is_null($imageFiles)) {
            return $return;
        }

        foreach ($imageFiles as $imageFile) {
            $disk = Config::get("filesystems.disks.{$imageFile->disk}");

            $storage = $disk['root'];

            $data = new \stdClass;

            $data->size_name =$imageFile->size_name;
            $data->category =$imageFile->category;
            $data->group =$imageFile->group;
            $data->client_original_name = $imageFile->client_original_name;
            $data->source = null;
            
            // check disk if visibility is public
            if (isset($disk['visibility'])) {
                if ($disk['visibility'] == 'public') {
                    $data->source = $disk['url'] . '/' . $imageFile->path;
                }
            }

            if (is_null($data->source)) {
                // route
                $data->source ='xxx';
            }

            $return->push($data);
        }


        return $return;
    }

    private function _getImage()
    {
        $images = $this
            ->_model
            ->images
            ->first();
            

        if (is_null($images)) {
            return null;
        }
        $images = $images
            ->imageFiles()
            ->select('disk', 'path', 'category', 'group', 'size_name', 'client_original_name');

        if (!is_null($this->_name)) {
            $images = $images->where('size_name', $this->_name);
        }



        return $images->get();
    }
}
