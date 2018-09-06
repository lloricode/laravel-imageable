<?php

namespace Lloricode\LaravelImageable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Getter
{
    private $_model;
    private $_name;
    private $_group;
    private $_category;

    public function __construct(Model $model, string $name)
    {
        $this->_model = $model;
        $this->_name = $name;
    }

    public function setGroup($group)
    {
        $this->_group = $group;
    }

    public function setCategory($category)
    {
        $this->_category = $category;
    }

    public function result()
    {
        $imageFile = $this->_getImage();

        $disk = Config::get("filesystems.disks.{$imageFile->disk}");

        $storage = $disk['root'];

        $src = null;

        // check disk if visibility is public
        if (isset($disk['visibility'])) {
            if ($disk['visibility'] == 'public') {
                $src = $disk['url'] . '/' . $imageFile->path;
            }
        }

        if (is_null($src)) {
            // route
            $src ='xxx';
        }


        return (object) [
            'src' => $src,
        ];
    }

    private function _getImage()
    {
        return $this
            ->_model
            ->images
            ->first()
            ->imageFiles()
            ->where('size_name', $this->_name)
            ->first();
    }
}
